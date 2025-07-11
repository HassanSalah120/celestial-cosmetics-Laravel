<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Facades\Settings;

class AddTranslationKeysToMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $menuStructure = json_decode(Settings::get('header_menu_structure', '[]'), true);
        
        if (empty($menuStructure)) {
            $this->command->info('No menu structure found to update');
            return;
        }
        
        // Add translation keys to menu items
        foreach ($menuStructure as &$item) {
            // Main menu items
            if (!isset($item['translation_key']) && isset($item['name'])) {
                // Convert item name to lowercase and use as translation key
                $translationKey = strtolower(str_replace(' ', '_', trim($item['name'])));
                $translationKey = str_replace('-', '_', $translationKey);
                $item['translation_key'] = $translationKey;
                $this->command->info("Added translation key '{$translationKey}' to menu item '{$item['name']}'");
            }
            
            // Dropdown items
            if (isset($item['dropdown_items']) && is_array($item['dropdown_items'])) {
                foreach ($item['dropdown_items'] as &$dropdownItem) {
                    if (!isset($dropdownItem['translation_key']) && isset($dropdownItem['name'])) {
                        // Convert dropdown item name to lowercase and use as translation key
                        $translationKey = strtolower(str_replace(' ', '_', trim($dropdownItem['name'])));
                        $translationKey = str_replace('-', '_', $translationKey);
                        $dropdownItem['translation_key'] = $translationKey;
                        $this->command->info("Added translation key '{$translationKey}' to dropdown item '{$dropdownItem['name']}'");
                    }
                }
            }
        }

        // Update the menu structure in the database
        Settings::set('header_menu_structure', json_encode($menuStructure));
        $this->command->info('Updated menu structure with translation keys');
    }
} 