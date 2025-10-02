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
     * Group modules by section (Overview, Access Control, Settings)
     */
    function groupModulesBySection($modules)
    {
        $grouped = [
            'overview' => [],
            'access_control' => [],
            'catalog' => [],
            'settings' => []
        ]; 

        foreach ($modules as $module) {
            // Dashboard is overview
            if ($module->name === 'dashboard') {
                $grouped['overview'][] = $module;
            }
            // Access control modules
            elseif (in_array($module->name, ['users', 'roles', 'permissions', 'modules', 'user-activities'])) {
                $grouped['access_control'][] = $module;
            }
            // Catalog modules
            elseif (in_array($module->name, ['products'])) {
                $grouped['catalog'][] = $module;
            }
            // Settings modules (generals, payments, shippings)
            else {
                $grouped['settings'][] = $module;
            }
        }

        return $grouped;
    }
}
