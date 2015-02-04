<form method="post" ng-cloak ng-controller="XSetExtensionsCtrl">
    <div class="action_header">
        <h2>{t}Set allowed extensions{/t}</h2>
        <fieldset ng-init="label='{t}Save changes{/t}'; loading=false;" class="buttons-form">
            <button class="button_main_action"
                    xim-button
                    xim-loading="loading"
                    xim-label="label"
                    xim-progress=""
                    xim-disabled=""
                    ng-click="saveChanges();">
        </fieldset>
    </div>

    <div ng-view ng-show="thereAreMessages" class="slide-item #/messageClass/# message">
        <p>#/message/#</p>
    </div>

    <div class="action_content" ng-init='commonAllowedExtensions={$commonAllowedExtensions}'>
        <p>{t}Set the extensions for the next Nodetypes{/t}:</p>
        <fieldset>
            <p ng-repeat="ext in commonAllowedExtensions">
                <label class="extension" for="#/ext.id/#">#/ext.description/#:</label>
                <input id="#/ext.id/#" class="extension" type="text" ng-model="ext.extension"/>
            </p>
        </fieldset>
        <p><strong>{t}Note:{/t}</strong> {t}the * value means any extension, empty value means none extension. You can't put ambiguous values.{/t}</p>
    </div>
</form>