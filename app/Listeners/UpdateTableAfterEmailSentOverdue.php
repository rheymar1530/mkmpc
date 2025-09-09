<?php

namespace App\Listeners;

use Illuminate\Mail\Events\MessageSent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;

class UpdateTableAfterEmailSentOverdue implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    use InteractsWithQueue;

    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(MessageSent $event)
    {
       $id_member = $event->message->getHeaders()->get('X-Message-ID');
       info("THISSISIISSIISS");
        DB::table('overdue_email')
        ->where('id_member',$id_member)
        ->where('status',0)
        ->update(['status'=>1]);
    }
}
