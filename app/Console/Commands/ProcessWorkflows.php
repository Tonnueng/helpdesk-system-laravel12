<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\WorkflowService;

class ProcessWorkflows extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'workflows:process';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process due workflows and execute next steps';

    /**
     * The workflow service instance.
     *
     * @var WorkflowService
     */
    protected $workflowService;

    /**
     * Create a new command instance.
     */
    public function __construct(WorkflowService $workflowService)
    {
        parent::__construct();
        $this->workflowService = $workflowService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting workflow processing...');

        try {
            $this->workflowService->processDueWorkflows();
            $this->info('Workflow processing completed successfully!');
            return 0;
        } catch (\Exception $e) {
            $this->error('Workflow processing failed: ' . $e->getMessage());
            return 1;
        }
    }
}