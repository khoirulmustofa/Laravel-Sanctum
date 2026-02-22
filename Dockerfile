FROM kmdocker2021/laravel-runtime:php84-n22

# Set working directory
WORKDIR /var/www/html

# 2. Copy application
COPY . .

# 4. Install PHP Dependencies
RUN composer install --no-scripts --no-dev --optimize-autoloader

# 5. Build Assets (NPM)
# We combine these to keep layers small and remove node_modules after build if not needed 
# (Note: Keep node_modules if your app needs them at runtime for some reason)
RUN npm install && \
    npm run build

# 6. Create Storage Structure & Set Permissions
# Combining these into one RUN command significantly saves disk space
# Buat folder yang diperlukan
RUN mkdir -p storage/app/public \
             storage/framework/cache \
             storage/framework/sessions \
             storage/framework/views \
             storage/logs \
             bootstrap/cache \
    touch storage/logs/worker.log storage/logs/laravel.log

# OPTIMASI: Hanya chown folder yang butuh akses tulis
# Jangan chown seluruh /var/www/html agar vendor/ & node_modules/ tidak ikut diproses
RUN chown -R www-data:www-data storage bootstrap/cache && \
    chmod -R 775 storage bootstrap/cache