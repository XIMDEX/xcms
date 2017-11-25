<form method="post" ng-cloak ng-controller="XSetExtensionsCtrl">
    <div class="action_header">
        <h5 class="direction_header"> Name Node: {t}Allowed extensions{/t}</h5>
        <h5 class="nodeid_header"> ID Node: {$nodeid}</h5>
        <hr>

    </div>

    <div ng-view ng-show="thereAreMessages" class="slide-item #/messageClass/# message">
        <p>#/message/#</p>
    </div>

    <div class="action_content" ng-init='commonAllowedExtensions={$commonAllowedExtensions}'>
            <div class="row tarjeta">
                <h2 class="h2_general">{t}Set the extensions for the next Nodetypes{/t}</h2>

            <div class="small-12 columns" ng-repeat="ext in commonAllowedExtensions">
                <div class="input">
                <label class="extension label_title label_general" for="#/ext.id/#">#/ext.description/#</label>
                <input id="#/ext.id/#" class="extension full_size input_general" type="text" ng-model="ext.extension"/>
                </div></div>


                <div class="small-8 columns">
                    <div class="alert alert-info">
                        <strong>{t}Info:{/t}</strong> {t}the * value means any extension, empty value means none extension. You can't put ambiguous values.{/t}</div>
                </div>
                <div class="small-4 columns">
        <fieldset ng-init="label='{t}Save changes{/t}'; loading=false;" class="buttons-form">
            <button class="btn main_action ui-state-default ui-corner-all button submit-button ladda-button btn_margin"
                    xim-button
                    xim-loading="loading"
                    xim-label="label"
                    xim-progress=""
                    xim-disabled=""
                    ng-click="saveChanges();">
        </fieldset></div>
</form>


