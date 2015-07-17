/** @jsx React.DOM */
angular.module('ximdex.common.directive').factory('TreeNode', ['$filter',
    function($filter) {
        var TreeNode = React.createClass({
            propTypes : {
                node: React.PropTypes.object.isRequired
            },
            getDefaultProps: function() {
                return { node: {}};
            },
            componentDidMount: function() {
                var that = this;
                this.divRootHammer = new Hammer(this.refs.divRoot.getDOMNode());
                this.divRootHammer.on('doubletap',
                    function(ev){
                        angular.element('#angular-tree').isolateScope().toggleNode(that.props.node,ev);
                    });
                this.divRootHammer.on('press', function(ev){angular.element('#angular-tree').isolateScope().loadActions(that.props.node,ev);});
                this.spanTriangleHammer = new Hammer(this.refs.spanTriangle.getDOMNode());
                this.spanTriangleHammer.on('tap', function(ev){angular.element('#angular-tree').isolateScope().toggleNode(that.props.node,ev);});
            },
            componentWillUnmount: function() {
                this.divRootHammer.stop();
                this.divRootHammer.destroy();
                this.divRootHammer = null;
                this.spanTriangleHammer.stop();
                this.spanTriangleHammer.destroy();
                this.spanTriangleHammer = null;
            },
            render: function() {
                var childNodes;
                if (this.props.node.childNodes != null) {
                    childNodes = this.props.node.collection.map(function(node, index) {
                        return <li key={index}><TreeNode node={node} /></li>
                    });
                }
                var loading = '';
                if(this.props.node.showNodes && this.props.node.loading){
                    loading = '<ul className="xim-treeview-loading" id="treeloading-undefined"><img src="xmd/images/browser/hbox/loading.gif"></ul>';
                }
                var iconClasses = "xim-treeview-icon icon-"+this.props.node.icon;
                return (
                    <div ref="divRoot">
                        <span ref="spanTriangle" className="ui-icon xim-actions-toggle-node ui-icon-triangle-1-e"></span>
                        <span className={iconClasses}></span>
                        <span className="xim-treeview-branch"
                            dangerouslySetInnerHTML={{__html: this.props.node.name}}></span>
                        <ul className="xim-treeview-branch">
                            {childNodes}
                        </ul>
                        {loading}
                    </div>
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