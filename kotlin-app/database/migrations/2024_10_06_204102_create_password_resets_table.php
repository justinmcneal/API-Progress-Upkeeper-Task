    <?php

    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;
    
    class CreatePasswordResetsTable extends Migration
    {
        public function up()
        {
            Schema::create('password_resets', function (Blueprint $table) {
                $table->id();
                $table->string('email')->index();
                $table->string('otp');
                $table->boolean('otp_verified')->default(false);
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
                $table->timestamp('expires_at')->nullable(); // Added for OTP expiration
            });
        }        
    
        public function down()
        {
            Schema::dropIfExists('password_resets');
        }
    }
    

