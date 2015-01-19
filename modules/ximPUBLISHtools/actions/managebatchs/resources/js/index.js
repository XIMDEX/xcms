/**
 *  \details &copy; 2011  Open Ximdex Evolution SL [http://www.ximdex.org]
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
 */


        X.actionLoaded(function (event, fn, params) {
            console.log(params.actionView.url, {method: 'getFrameList'});
            var scope = X.angularTools.initView(params.context, params.tabId);
//            var controller = scope.$$childHead;
//            controller.start(params);


            return;
            
            
            
            var extended = {};
            var content = params.context;

            $('document').ready(function () {
                get_frame_list(true);
//                periodical_refresh_list(5000);

                $dialog = $('<div></div>').dialog({
                    autoOpen: false,
                    modal: true,
                    resizable: false,
                    draggable: false,
                    open: function (event, ui) {
                        jQuery('.ui-dialog-titlebar-close').hide();
                    }
                });
            });

            function periodical_refresh_list(interval) {
                var t = XimTimer.getInstance();
                t.removeAllObservers();
                t.addObserver(get_frame_list, interval);
                t.start();
            }

            function refresh_frame_list(data, initial_state) {
                var progress_table = [];
                progress_table['In'] = 100;
                progress_table['Pending'] = 0;
                progress_table['Due2In'] = 25;
                progress_table['Due2In_'] = 50;
                progress_table['Due2Out'] = 25;
                progress_table['Due2Out_'] = 50;
                progress_table['Pumped'] = 75;
                $('div#frame_list', content).empty();

                for (var portal_version in data) {
                    if (initial_state)
                        extended[portal_version] = false;
                    var acum_progress = 0;
                    var nodename = '';
                    var batchid = 0;
                    var batch_state = 'activa';
                    var triangle_icon = extended[portal_version] ? 'ui-icon ui-icon-triangle-1-se' : 'ui-icon ui-icon-triangle-1-e';

                    $('div#frame_list', content).append('<div class="batch_container"><span class="' + triangle_icon + '" id="portal_version_' + portal_version + '"></span><div id="bar_pv_' + portal_version + '" class="progressbar"></div><div class="frame_filename"></div><div class="frame_default"></div></div>');

                    $(data[portal_version]).each(function (k, item) {
                        if (item.Progress === '-1')
                            item.Progress = '0';
                        $('div#frame_list', content).append('<div class="portal_version_' + portal_version + '"><span class="frame_indent"></span><div id="bar_' + item.IdSync + '" class="progressbar"></div><div class="frame_filename">' + get_elapsed(item.PubTime) + ' <strong><em>' + item.FilePath + '/' + item.FileName + '</em></strong></div><div class="frame_default"></div></div>');
                        $('#bar_' + item.IdSync, content).progressbar({value: parseInt(item.Progress)});
                        if (item.Error === 1)
                            $('#bar_' + item.IdSync, content).css({'background': '#F50202'});
                        acum_progress += parseInt(item.Progress);
                        nodename = item.NodeName;
                        batchid = item.IdBatch;
                        batch_state = item.BatchState;
                    });

                    $('#bar_pv_' + portal_version, content).progressbar({value: acum_progress / $(data[portal_version]).length});
                    $('#bar_pv_' + portal_version, content).next().append('<strong><em>' + nodename + '</em></strong>');
                    $('#bar_pv_' + portal_version, content).next().append(' <em>Esta publicación está <strong>' + batch_state + '</strong></em> <a class="batch_toggle" id="batchid_' + batchid + '">[' + ((batch_state == 'detenida') ? 'Reanudar' : 'Detener') + ' esta publicación]</a>');

                    if (extended[portal_version] === false)
                        $('.portal_version_' + portal_version, content).hide();

                    $('#portal_version_' + portal_version, content).click(function (e) {
                        $('.' + $(e.currentTarget).attr('id')).toggle();
                        var pv = $(e.currentTarget).attr('id').replace('portal_version_', '');
                        $(this).toggleClass('ui-icon ui-icon-triangle-1-e ui-icon ui-icon-triangle-1-se');
                        extended[pv] = (extended[pv] === false) ? true : false;
                    });
                }


                $dialog.dialog('close');

                $('.batch_toggle', content).click(function (e) {
                    get_frame_list(initial_state, parseInt(($(e.target).attr('id')).replace('batchid_', '')));
                    $dialog.text("Un momento, por favor...");
                    $dialog.dialog('open');
                });

                /*$('.progressbar', content).click(function(e) {
                 var id = $(this).attr('id');
                 if(id.replace('bar_pv', '') != id) {
                 $(this).parent().toggleClass('batch_container_selected');
                 }
                 });*/

            }

            function get_frame_list(initial_state, batch_id) {
                $.getJSON(params.actionView.url, {method: 'getFrameList', batchid: batch_id})
                        .done(function (data) {
                            var total = 0, completed = 0;
                            $.each(data, function (k, arr) {
                                $.each(arr, function (i, frame) {
                                    total++;
                                    if (frame.Progress === '100') {
                                        completed++;
                                    }
                                });
                            });
//                            console.log({completed: completed, total: total});
                            if (completed === total) {
                                var t = XimTimer.getInstance();
                                t.getObserver(get_frame_list);
                                t.stop();
                            }
                            refresh_frame_list(data, initial_state);
                        });
            }

            function get_elapsed(seconds) {
                var secs = parseInt(seconds);
                var mins = parseInt(secs / 60);
                secs = secs - (mins * 60);
                return mins + 'm' + secs + 's';
            }
        });

