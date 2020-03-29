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
        $fetchedResult = PageFetchingService::fetch($domain->name);

        if (!$fetchedResult->isSuccessful()) {
            return;
        }

        $html = $fetchedResult->getResult()->get('html');
        $analysisResult = PageAnalysisService::analyze($html);

        $domainCheck = $analysisResult
            ->getResult()
            ->merge($fetchedResult->getResult()->except('html'))
            ->put('domainId', $domain->id);

        $domainCheck = DomainCheck::initializeWith($domainCheck);
        DomainCheckRepository::store($domainCheck);
    }
}
