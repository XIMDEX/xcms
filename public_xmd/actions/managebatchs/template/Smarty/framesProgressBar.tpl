{**
*  \details &copy; 2018 Open Ximdex Evolution SL [http://www.ximdex.org]
*
*  Ximdex a Semantic Content Management System (CMS)
*
*  This program is free software: you can redistribute it and/or modify
*  it under the terms of the GNU Affero General Public License as published
*  by the Free Software Foundation, either version 3 of the License, or
*  (at your option) any later version.
*
*  This program is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU Affero General Public License for more details.
*
*  See the Affero GNU General Public License for more details.
*  You should have received a copy of the Affero GNU General Public License
*  version 3 along with Ximdex (see LICENSE file).
*
*  If not, visit http://gnu.org/licenses/agpl-3.0.html.
*
*  @author Ximdex DevTeam <dev@ximdex.com>
*  @version $Revision$
*}

<div ng-if="frames.success > 0" class="progress-bar progress-bar-striped progress-bar-success" role="progressbar" 
        style="width: #/ frames.success * 100 / frames.total /#%" title="#/frames.success/# documents success">
        #/frames.success/# success
</div>
<div ng-if="frames.soft > 0" class="progress-bar progress-bar-striped progress-bar-errored-soft active" 
        role="progressbar" style="width: #/ frames.soft * 100 / frames.total /#%" 
        title="#/ frames.soft /# documents with soft errors">
    #/frames.soft/# soft errors
</div>
<div ng-if="frames.fatal > 0" class="progress-bar progress-bar-striped progress-bar-errored-fatal" 
        role="progressbar" style="width: #/ frames.fatal * 100 / frames.total /#%" 
        title="#/frames.fatal/# documents with fatal errors">
    #/frames.fatal/# fatal errors
</div>
<div ng-if="frames.active > 0" class="progress-bar progress-bar-striped progress-bar-active active" role="progressbar" 
        style="width: #/ frames.active * 100 / frames.total /#%" title="#/frames.active/# documents active">
    #/frames.active/# active
</div>
<div ng-if="frames.pending > 0" class="progress-bar progress-bar-striped progress-bar-pending active" 
        role="progressbar" style="width: #/ frames.pending * 100 / frames.total /#%" 
        title="#/frames.pending/# documents pending">
    #/frames.pending/# pending
</div>