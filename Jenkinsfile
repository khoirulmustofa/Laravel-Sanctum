pipeline {
    agent any

    environment {
        SERVER_USER  = 'nfbsb'
        SERVER_HOST  = '192.168.100.125'
        PROJECT_PATH = '/home/nfbsb/php/backend_app'
        GIT_REPO     = 'git@github.com:khoirulmustofa/Laravel-Sanctum.git'
    }

    stages {
        stage('Deploy via SSH') {
            steps {
                sshagent (credentials: ['serverpaklukman']) {
                    sh """
                        ssh -o StrictHostKeyChecking=no ${SERVER_USER}@${SERVER_HOST} '
                            
                            # === Langkah 1: Sinkronisasi Kode Git ===
                            if [ ! -d "${PROJECT_PATH}" ] || [ ! -d "${PROJECT_PATH}/.git" ]; then
                                echo "[INFO] Proyek tidak ditemukan. Cloning baru..."
                                rm -rf "${PROJECT_PATH}"
                                git clone ${GIT_REPO} ${PROJECT_PATH}
                            else
                                echo "[INFO] Proyek ditemukan. Pulling updates..."
                                cd ${PROJECT_PATH}
                                git fetch origin
                                git reset --hard origin/main
                                git clean -fd
                            fi

                            # === Langkah 2: Pindah ke Direktori & Setup ENV ===
                            cd "${PROJECT_PATH}"
                            
                            # --- BAGIAN KRUSIAL ---
                            # Kita salin file asli ke folder project agar bisa dibaca saat Docker Build
                            echo "[INFO] Menyalin file environment untuk proses build..."
                            if [ -f "/home/nfbsb/.env/.env.service" ]; then
                                cp /home/nfbsb/.env/.env.service .env
                            else
                                echo "[ERROR] File /home/nfbsb/.env/.env.service tidak ditemukan!"
                                exit 1
                            fi

                            # === Langkah 3: Manajemen Container (Stop) ===
                            echo "[INFO] Menghentikan container lama..."
                            if [ -f docker-compose.yml ]; then
                                docker compose down
                            fi

                            # === Langkah 3.5: Hapus Volume Lama ===
                            echo "[INFO] Membersihkan sampah Docker (Prune)..."
                            docker system prune -f

                            # === Langkah 4: Build & Run Container ===
                            # Sekarang npm run build di Dockerfile tidak akan error
                            echo "[INFO] Membangun dan menjalankan container baru..."
                            docker compose up -d --build

                            # Memberikan jeda waktu agar container stabil
                            sleep 5

                            # === Langkah 5: Pengaturan Hak Akses (Permissions) ===
                            echo "[INFO] Mengatur hak akses file di dalam container..."
                            if [ \$(docker ps -q -f name=backend_app) ]; then
                                docker exec backend_app chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
                                docker exec backend_app chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache
                                                                                                
                                # === Langkah 6: Symlink Storage ===
                                docker exec backend_app php artisan storage:link
                                
                                # (Opsional) Hapus file .env di host setelah selesai build demi keamanan
                                # rm .env

                                echo "[SUCCESS] Deployment selesai dengan sempurna!"
                            else
                                echo "[ERROR] Container backend_app gagal berjalan!"
                                docker compose logs
                                exit 1
                            fi
                        '
                    """
                }
            }
        }
    }
}