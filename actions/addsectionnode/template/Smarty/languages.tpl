<div class="languages-available col1-3 right">
                {if $languageCount neq 0}
                        <h3>{t}Languages availables{/t}</h3>
                        {foreach from=$languageOptions item=languageOption}
                        <div class="languages-section">
                                <input name="langidlst[]" type='checkbox' value="{$languageOption.IdLanguage}" class="hidden-focus" id="{$languageOption.IdLanguage}">
                                <label for="{$languageOption.IdLanguage}" class="icon checkbox-label">{$languageOption.Name|gettext}</label>                                  <input type="text" name="namelst[{$languageOption.IdLanguage}]" class="alternative-name" placeholder="{t}Alternative name for paths &amp; breadcrumbs{/t}">                                                                                                                         </div>
                        {/foreach}

                {else}
                        <p>{t}There are no languages associated to this project.{/t}</p>
                {/if}
                </div>
