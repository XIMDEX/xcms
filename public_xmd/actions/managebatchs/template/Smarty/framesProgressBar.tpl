{* Frames progress bar *}
<div ng-if="frames.success > 0" class="progress-bar progress-bar-striped progress-bar-success" role="progressbar" 
        style="width: #/ frames.success * 100 / frames.total | number:6 /#%;" title="#/ frames.success /# documents success">
        #/ frames.success /# success
</div>
<div ng-if="frames.fatal > 0" class="progress-bar progress-bar-striped progress-bar-errored-fatal" 
        role="progressbar" style="width: #/ frames.fatal * 100 / frames.total | number:6 /#%;" 
        title="#/ frames.fatal /# documents with fatal errors">
    #/ frames.fatal /# fatal errors
</div>
<div ng-if="frames.stopped > 0" class="progress-bar progress-bar-striped progress-bar-stopped" 
        role="progressbar" style="width: #/ frames.stopped * 100 / frames.total | number:6 /#%;" 
        title="#/ frames.stopped /# documents stopped">
    #/ frames.stopped /# stopped
</div>
<div ng-if="frames.soft > 0" class="progress-bar progress-bar-striped progress-bar-errored-soft" 
        role="progressbar" style="width: #/ frames.soft * 100 / frames.total | number:6 /#%;" 
        title="#/ frames.soft /# documents with soft errors">
    #/ frames.soft /# soft errors
</div>
<div ng-if="frames.delayed > 0" class="progress-bar progress-bar-striped progress-bar-delayed" 
        role="progressbar" style="width: #/ frames.delayed * 100 / frames.total | number:6 /#%;" 
        title="#/ frames.delayed /# documents delayed">
    #/ frames.delayed /# delayed
</div>
<div ng-if="frames.active > 0" class="progress-bar progress-bar-striped progress-bar-active active" role="progressbar" 
        style="width: #/ frames.active * 100 / frames.total | number:6 /#%;" title="#/ frames.active /# documents active">
    #/ frames.active /# active
</div>
<div ng-if="frames.pending > 0" class="progress-bar progress-bar-striped progress-bar-pending" 
        role="progressbar" style="width: #/ frames.pending * 100 / frames.total | number:6 /#%;" 
        title="#/ frames.pending /# documents pending">
    #/ frames.pending /# pending
</div>