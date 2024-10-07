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
                $table->boolean('otp_verified')->default(false); // Added to track OTP verification
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable(); // Added to track updates
            });
        }
    
        public function down()
        {
            Schema::dropIfExists('password_resets');
        }
    }
    

