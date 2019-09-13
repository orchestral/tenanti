<?php

namespace Orchestra\Tenanti\Jobs\Queueable;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use Orchestra\Tenanti\Jobs\CreateTenant as Job;

class CreateTenant extends Job implements ShouldQueue
{
    use SerializesModels;
}
