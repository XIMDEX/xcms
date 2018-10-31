{* Batchs progress bar *}
<div class="batch-progress-bar progress-bar" role="progressbar" ng-class="{literal}{
        'progress-bar-pending': batch == 'Waiting', 
        'progress-bar-active': batch == 'InTime',
        'progress-bar-closing': batch == 'Closing',
        'progress-bar-success': batch == 'Ended',
        'progress-bar-errored-soft': batch == 'NoFrames',
        'progress-bar-stopped': batch == 'Stopped',
        'progress-bar-delayed': batch == 'Delayed'
    }{/literal}"
    ng-repeat="batch in frames.batchs" 
    style="width: #/ 100 / frames.totalBatchs | number:8 /#%;"
    title="#/ batch /#">
    ·
</div>