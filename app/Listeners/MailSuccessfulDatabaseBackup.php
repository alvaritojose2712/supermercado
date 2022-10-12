<?php

namespace App\Listeners;

set_time_limit(15000);
// use Illuminate\Contracts\Queue\ShouldQueue;
// use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use Spatie\Backup\Events\BackupZipWasCreated;

use App\Models\sucursal;
use Response;


class MailSuccessfulDatabaseBackup
{
    

    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  BackupZipWasCreated  $event
     * @return void
     */
    public function handle(BackupZipWasCreated $event)
    {
        $this->mailBackupFile($event->pathToZip);
    }

    public function mailBackupFile($path)
    {
        try {
            // $sends =(new PedidosController)->sends;
            $sucursal = sucursal::all()->first();
            $text = $sucursal->sucursal." | RESPALDO | ".date("Y-m-d");

            Mail::raw($text,   function ($message) use ($path,$sucursal,$text) {

                $message->to($sucursal->correo)
                    ->subject($text)
                    ->attach($path);
            });

            return Response::json(["msj"=>"Éxito al respladar","estado"=>true]);
        } catch (\Exception $exception) {
            throw $exception;
        }

    }
}
