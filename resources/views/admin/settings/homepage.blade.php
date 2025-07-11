                    <!-- Homepage Configuration -->
                    <div class="mb-4">
                        <h3 class="text-lg font-semibold mb-2">Homepage Configuration</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="form-group">
                                <label for="featured_products_count" class="block mb-1">Featured Products Count</label>
                                <input type="number" min="1" max="20" name="featured_products_count" id="featured_products_count" 
                                    class="form-control" value="{{ $settings['featured_products_count'] ?? 8 }}">
                            </div>
                            
                            <div class="form-group">
                                <label for="new_arrivals_count" class="block mb-1">New Arrivals Count</label>
                                <input type="number" min="1" max="20" name="new_arrivals_count" id="new_arrivals_count" 
                                    class="form-control" value="{{ $settings['new_arrivals_count'] ?? 4 }}">
                            </div>
                            
                            <div class="form-group">
                                <label for="new_product_days" class="block mb-1">New Product Days</label>
                                <input type="number" min="1" max="90" name="new_product_days" id="new_product_days" 
                                    class="form-control" value="{{ $settings['new_product_days'] ?? 30 }}" 
                                    placeholder="30">
                                <small class="text-muted">Number of days a product is considered "new" after creation</small>
                            </div>
                            
                            <div class="form-group">
                                <label for="featured_categories_count" class="block mb-1">Featured Categories Count</label>
                                <input type="number" min="1" max="10" name="featured_categories_count" id="featured_categories_count" 
                                    class="form-control" value="{{ $settings['featured_categories_count'] ?? 3 }}">
                            </div>
                        </div>
                    </div> 