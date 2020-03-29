<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\DomainCheck;
use App\Services\PageAnalysisService;
use App\Services\PageFetchingService;
use App\Repositories\DomainRepository;
use App\Repositories\DomainCheckRepository;

class AnalyzePageJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public $tries = 1;
    protected $domainId;

    public function __construct($domainId)
    {
        $this->domainId = $domainId;
    }

    public function handle()
    {
        $domain = DomainRepository::findOrFail($this->domainId);
        $fetchResult = PageFetchingService::fetch($domain->name);

        if (!$fetchResult->isSuccessful()) {
            return;
        }

        $body = $fetchResult->getResult()->get('body');
        $analysisResult = PageAnalysisService::analyze($body);

        $domainCheck = $analysisResult
            ->getResult()
            ->merge($fetchResult->getResult()->except('body'))
            ->put('domainId', $domain->id);

        $domainCheck = DomainCheck::fromArray($domainCheck->toArray());
        DomainCheckRepository::store($domainCheck);
    }
}
