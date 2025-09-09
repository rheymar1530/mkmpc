
				cd C:/Program Files/wkhtmltopdf/bin
				wkhtmltopdf "lse.libcapsystems.net/admin/soa/soa_attachment/ba9049ad49fdddfe4bc79c397369dac1ba9049ad49fdd" C:/wamp64/www/LSE_NEW_BACKEND/storage/app/public/soa_attachment/CTRL_0000048_ba9049ad49fdddfe4bc79c397369dac1ba9049ad49fdd.pdf
				cd C:\wamp64\www\LSE_NEW_BACKEND
				C:\wamp64\bin\php\php7.4.9\php.exe artisan soa:update_generated --token=ba9049ad49fdddfe4bc79c397369dac1ba9049ad49fdd
				exit;