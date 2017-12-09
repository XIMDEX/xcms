angular.module('ximdex.common.directive').factory('TreeNode', ['$filter',
    function($filter) {
        var scope = null;
        var root = null;

        var TreeNode = React.createClass({
            propTypes : {
                node: React.PropTypes.object.isRequired,
                selected: React.PropTypes.object.isRequired
            },
            getDefaultProps: function() {
                return {
                    node: {},
                    selected: []
                };
            },
            componentDidMount: function() {
                if(root == null)
                    this.root = this;
                if($(this.getDOMNode()).closest('.ng-isolate-scope').scope()){
                    scope = $(this.getDOMNode()).closest('.ng-isolate-scope').scope();
                }else{
                    scope = $(this.getDOMNode()).closest('.ng-isolate-scope').isolateScope()
                }

                var that = this;

                this.divRootHammer = new Hammer(this.refs.divRoot.getDOMNode());
                this.divRootHammer.on('tap', function(ev){
                    ev.preventDefault();
                    scope.select(that.props.node,ev);
                    scope.$digest();
                    //that.root.forceUpdate();
                });
                this.divRootHammer.on('doubletap',
                    function(ev){
                        scope.toggleNode(that.props.node,ev);
                        scope.$digest();
                    });
                this.divRootHammer.on('press', function(ev){
                    scope.loadActions(that.props.node,ev);
                    scope.$digest();
                });
                angular.element(this.refs.divRoot.getDOMNode()).bind('contextmenu', function(event) {
                    scope.$apply(function() {
                        event.preventDefault();
                        scope.loadActions(that.props.node,event);
                    });
                });
                angular.element(this.refs.divRoot.getDOMNode()).bind('contextmenu', function(event) {
                    scope.$apply(function() {
                        event.preventDefault();
                        scope.loadActions(that.props.node,event);
                    });
                });

                angular.element(this.refs.icon.getDOMNode()).bind('click', function(event) {
                    scope.$apply(function() {
                        event.preventDefault();
                        scope.loadActions(that.props.node,event);
                    });
                });

                this.spanTriangleHammer = new Hammer(this.refs.spanTriangle.getDOMNode());
                this.spanTriangleHammer.on('tap', function(ev){
                    scope.toggleNode(that.props.node,ev);
                    scope.$digest();
                });
            },
            componentWillUnmount: function() {
                this.divRootHammer.stop();
                this.divRootHammer.destroy();
                this.divRootHammer = null;
                this.spanTriangleHammer.stop();
                this.spanTriangleHammer.destroy();
                this.spanTriangleHammer = null;
                angular.element(this.refs.divRoot.getDOMNode()).unbind();
            },
            render: function() {
                if(root == null)
                    root = this;

                var cx          = React.addons.classSet;
                var childNodes  = '';
                var loading     = '';
                var arrow       = '';

                // childNodes
                if (this.props.node.showNodes && this.props.node.collection != null) {
                    var that = this;
                    childNodes = this.props.node.collection.map(function(node, index) {
                        return React.createElement("li", {key: index}, React.createElement(TreeNode, {node: node,selected: that.props.selected}))
                    });
                }

                // loading
                if(this.props.node.showNodes && this.props.node.loading){
                    loading = React.createElement('span',{className: 'xim-treeview-loading'},
                        React.createElement('img',{src: window.com.ximdex.baseUrl + '/public_xmd/assets/images/browser/hbox/loading.gif'})
                    );
                }

                // arrow
                if(this.props.node.children){
                    arrow = React.createElement('span',{className: this.dropDownClasses})

                    /*loading = React.createElement('ul',{className: 'xim-treeview-loading'},
                        React.createElement('img',{src: window.com.ximdex.baseUrl + '/public_xmd/assets/images/browser/hbox/loading.gif'})
                    );*/
                }

                var iconClasses = "xim-treeview-icon icon-"+this.props.node.icon;
                /*var selected = false;
                 var selectedNodes = this.props.selected;
                 for(var i = 0; i<selectedNodes.length; i++){
                 if(this.props.node.nodeid == selectedNodes[i].nodeid){
                 selected = true;
                 break;
                 }
                 }*/
                var rootClasses = cx({
                    'xim-treeview-node': true,
                    'xim-treeview-container-selected': $filter("nodeSelected")(this.props.node, this.props.selected)
                });

                var dropDownClasses = cx({
                    'ui-icon xim-actions-toggle-node': true,
                    'ui-icon-triangle-1-e': true,
                    'ui-icon-triangle-1-se': this.props.node.showNodes,
                    'icon-hidden': !this.props.node.children || (this.props.node.collection && this.props.node.collection.length === 0)
                });

                return (
                    <span>
                        <div className={rootClasses} ref="divRoot">
                            {arrow}
                            <span className={iconClasses} ref="icon"></span>
                            <span className="xim-treeview-branch" dangerouslySetInnerHTML={{__html: this.props.node.name + (this.props.node.modified == '1' ? '*' : '')}}></span>
                        </div>
                        <ul className="xim-treeview-branch">
                            {childNodes}
                        </ul>
                        {loading}
                    </span>
                );
            }
        });
        return TreeNode
    }
]);

angular.module('ximdex.common.directive').directive( 'treeNode',
    ['reactDirective', function( reactDirective ) {
        return reactDirective('TreeNode');
    } ]);
