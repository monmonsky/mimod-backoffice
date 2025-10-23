<?php

namespace App\Repositories\Contracts\Menu;

interface MenuRepositoryInterface
{
    /**
     * Get all menus with optional filters
     */
    public function getAllMenus(array $filters = []);

    /**
     * Get menu by ID
     */
    public function findById(int $id);

    /**
     * Get menu tree (nested structure) by location
     */
    public function getMenuTree(string $location = 'header', bool $activeOnly = true);

    /**
     * Get parent menus only
     */
    public function getParentMenus(array $filters = []);

    /**
     * Create new menu
     */
    public function create(array $data);

    /**
     * Update menu
     */
    public function update(int $id, array $data);

    /**
     * Delete menu
     */
    public function delete(int $id);

    /**
     * Reorder menus
     */
    public function reorder(array $orders);

    /**
     * Bulk create menus from categories
     */
    public function bulkCreateFromCategories(array $data);

    /**
     * Bulk create menus from brands
     */
    public function bulkCreateFromBrands(array $data);

    /**
     * Get menus for dropdown/select (admin)
     */
    public function getMenusForSelect();

    /**
     * Build menu tree from flat array
     */
    public function buildTree(array $elements, int $parentId = null);
}
