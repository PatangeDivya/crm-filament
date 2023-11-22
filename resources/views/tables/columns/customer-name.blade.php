<div class="fi-ta-text-item inline-flex items-center gap-1.5 text-sm text-gray-950 dark:text-white">
    {{ $getRecord()->first_name }} {{ $getRecord()->last_name }} 
    <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset ring-gray-500/10">{{ $getRecord()->pipelineStage->name }}</span>
</div>
