<?php
 
namespace Saidtech\Zpay\Console\Commands;

 
use App\Models\User;
use App\Support\DripEmailer;
use Illuminate\Console\Command;
 
class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zpay:init ';
 
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a marketing email to a user';
 
    /**
     * Execute the console command.
     */
    public function handle(DripEmailer $drip): void
    {
        $drip->send(User::find($this->argument('user')));
    }
}