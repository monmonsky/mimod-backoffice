<?php

if (!function_exists('renderSidebarMenu')) {
    /**
     * Render sidebar menu from modules data
     */
    function renderSidebarMenu($modules, $currentRoute = null)
    {
        if (empty($modules)) {
            return '';
        }

        $html = '';
        $currentRoute = $currentRoute ?? request()->route()->getName();

        foreach ($modules as $module) {
            // Skip inactive or invisible modules
            if (!$module->is_active || !$module->is_visible) {
                continue;
            }

            // Check if module has children
            $hasChildren = isset($module->children) && count($module->children) > 0;

            if ($hasChildren) {
                // Parent module with children (collapsible)
                $isExpanded = checkIfMenuExpanded($module, $currentRoute);

                $html .= '<div class="group collapse">';
                $html .= '<input aria-label="Sidemenu item trigger" type="checkbox" class="peer" name="sidebar-menu-' . $module->name . '" ' . ($isExpanded ? 'checked' : '') . ' />';
                $html .= '<div class="collapse-title px-2.5 py-1.5">';

                if ($module->icon) {
                    $html .= '<span class="iconify ' . $module->icon . ' size-4"></span>';
                }

                $html .= '<span class="grow">' . $module->display_name . '</span>';
                $html .= '<span class="iconify lucide--chevron-right arrow-icon size-3.5"></span>';
                $html .= '</div>';
                $html .= '<div class="collapse-content ms-6.5 !p-0">';
                $html .= '<div class="mt-0.5 space-y-0.5">';

                // Render children
                foreach ($module->children as $child) {
                    if (!$child->is_active || !$child->is_visible) {
                        continue;
                    }

                    $isActive = $child->route && request()->routeIs($child->route);
                    $html .= '<a class="menu-item ' . ($isActive ? 'active' : '') . '" href="' . ($child->route ? route($child->route) : '#') . '">';
                    $html .= '<span class="grow">' . $child->display_name . '</span>';
                    $html .= '</a>';
                }

                $html .= '</div>';
                $html .= '</div>';
                $html .= '</div>';
            } else {
                // Single menu item
                $isActive = $module->route && request()->routeIs($module->route . '*');

                $html .= '<a class="menu-item ' . ($isActive ? 'active' : '') . '" href="' . ($module->route ? route($module->route) : '#') . '">';

                if ($module->icon) {
                    $html .= '<span class="iconify ' . $module->icon . ' size-4"></span>';
                }

                $html .= '<span class="grow">' . $module->display_name . '</span>';
                $html .= '</a>';
            }
        }

        return $html;
    }
}

if (!function_exists('checkIfMenuExpanded')) {
    /**
     * Check if menu should be expanded based on current route
     */
    function checkIfMenuExpanded($module, $currentRoute)
    {
        if (!isset($module->children) || count($module->children) === 0) {
            return false;
        }

        foreach ($module->children as $child) {
            if ($child->route && request()->routeIs($child->route)) {
                return true;
            }
        }

        return false;
    }
}

if (!function_exists('groupModulesBySection')) {
    /**
     * Group modules by section dynamically based on group_name field
     * Automatically discovers and creates groups from database
     */
    function groupModulesBySection($modules)
    {
        $grouped = [];

        foreach ($modules as $module) {
            // Get group name from module, default to 'other' if not set
            $groupName = $module->group_name ?? 'other';

            // Initialize group array if it doesn't exist
            if (!isset($grouped[$groupName])) {
                $grouped[$groupName] = [];
            }

            $grouped[$groupName][] = $module;
        }

        // Sort groups by the minimum sort_order of modules in each group
        // This ensures groups appear in the sidebar in the correct order
        uksort($grouped, function($a, $b) use ($grouped) {
            $minA = collect($grouped[$a])->min('sort_order') ?? 999;
            $minB = collect($grouped[$b])->min('sort_order') ?? 999;
            return $minA <=> $minB;
        });

        return $grouped;
    }
}
