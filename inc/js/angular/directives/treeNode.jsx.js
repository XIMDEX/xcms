/** @jsx React.DOM */
angular.module('ximdex.common.directive').factory('TreeNode', ['$filter',
    function($filter) {
        var root = null;
        var TreeNode = React.createClass({displayName: 'TreeNode',
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
                if(root != null)
                    this.root = root
                var that = this;
                this.divRootHammer = new Hammer(this.refs.divRoot.getDOMNode());
                this.divRootHammer.on('tap', function(ev){
                    ev.preventDefault();
                    angular.element('#angular-tree').isolateScope().select(that.props.node,ev);
                    angular.element('#angular-tree').isolateScope().$digest();
                    that.root.forceUpdate();
                });
                this.divRootHammer.on('doubletap',
                    function(ev){
                        angular.element('#angular-tree').isolateScope().toggleNode(that.props.node,ev);
                        angular.element('#angular-tree').isolateScope().$digest();
                    });
                this.divRootHammer.on('press', function(ev){
                    angular.element('#angular-tree').isolateScope().loadActions(that.props.node,ev);
                    angular.element('#angular-tree').isolateScope().$digest();
                });
                angular.element(this.refs.divRoot.getDOMNode()).bind('contextmenu', function(event) {
                    angular.element('#angular-tree').isolateScope().$apply(function() {
                        event.preventDefault();
                        angular.element('#angular-tree').isolateScope().loadActions(that.props.node,event);
                    });
                });
                this.spanTriangleHammer = new Hammer(this.refs.spanTriangle.getDOMNode());
                this.spanTriangleHammer.on('tap', function(ev){
                    angular.element('#angular-tree').isolateScope().toggleNode(that.props.node,ev);
                    angular.element('#angular-tree').isolateScope().$digest();
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
                var childNodes;
                var cx = React.addons.classSet;
                var loading = '';
                if (this.props.node.showNodes && this.props.node.collection != null) {
                    var that = this;
                    childNodes = this.props.node.collection.map(function(node, index) {
                        return React.createElement("li", {key: index}, React.createElement(TreeNode, {node: node,selected: that.props.selected}))
                    });
                }

                if(this.props.node.showNodes && this.props.node.loading){
                    loading = React.createElement('ul',{className: 'xim-treeview-loading'},
                        React.createElement('img',{src: 'xmd/images/browser/hbox/loading.gif'})
                    );
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
                    'icon-hidden': !this.props.node.children && (this.props.node.collection == null || this.props.node.collection.length==0)
                });
                return (
                    React.createElement('span',{},
                        React.createElement("div", {className: rootClasses, ref: "divRoot"},
                            React.createElement("span", {ref: "spanTriangle", className: dropDownClasses}),
                            React.createElement("span", {className: iconClasses}),
                            React.createElement("span", {className: "xim-treeview-branch",dangerouslySetInnerHTML: {__html: this.props.node.name + (this.props.node.modified == '1' ? '*' : '')}})
                        ),
                        React.createElement("ul", {className: "xim-treeview-branch"},
                            childNodes
                        ),
                        loading
                    )
                );
            }
        });
        return TreeNode;
    }
]);

angular.module('ximdex.common.directive').directive( 'treeNode',
    ['reactDirective', function( reactDirective ) {
        return reactDirective('TreeNode',undefined,{templateUrl: '',
            replace: true});
} ]);
