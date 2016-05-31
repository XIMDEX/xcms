// Generated by CoffeeScript 1.10.0
describe('`ContentEdit.Root.get()`', function() {
  return it('should return a singleton instance of Root`', function() {
    var root;
    root = new ContentEdit.Root.get();
    return expect(root).toBe(ContentEdit.Root.get());
  });
});

describe('`ContentEdit.Root.focused()`', function() {
  return it('should return the currently focused element or null if no element has focus', function() {
    var element, region, root;
    region = new ContentEdit.Region(document.createElement('div'));
    element = new ContentEdit.Element('div');
    region.attach(element);
    root = new ContentEdit.Root.get();
    if (root.focused()) {
      root.focused().blur();
    }
    expect(root.focused()).toBe(null);
    element.focus();
    return expect(root.focused()).toBe(element);
  });
});

describe('`ContentEdit.Root.dragging()`', function() {
  return it('should return the element currently being dragged or null if no element is being dragged', function() {
    var element, region, root;
    region = new ContentEdit.Region(document.createElement('div'));
    element = new ContentEdit.Element('div');
    region.attach(element);
    root = new ContentEdit.Root.get();
    element.drag(0, 0);
    expect(root.dragging()).toBe(element);
    root.cancelDragging();
    return expect(root.dragging()).toBe(null);
  });
});

describe('`ContentEdit.Root.dropTarget()`', function() {
  return it('should return the element the dragging element is currently over', function() {
    var elementA, elementB, region, root;
    region = new ContentEdit.Region(document.getElementById('test'));
    elementA = new ContentEdit.Text('p');
    region.attach(elementA);
    elementB = new ContentEdit.Text('p');
    region.attach(elementB);
    root = new ContentEdit.Root.get();
    elementA.drag(0, 0);
    elementB._onMouseOver({});
    expect(root.dropTarget()).toBe(elementB);
    root.cancelDragging();
    expect(root.dropTarget()).toBe(null);
    region.detach(elementA);
    return region.detach(elementB);
  });
});

describe('`ContentEdit.Root.type()`', function() {
  return it('should return \'Region\'', function() {
    var root;
    root = new ContentEdit.Root.get();
    return expect(root.type()).toBe('Root');
  });
});

describe('`ContentEdit.Root.startDragging()`', function() {
  return it('should start a drag interaction', function() {
    var cssClasses, element, region, root;
    region = new ContentEdit.Region(document.getElementById('test'));
    element = new ContentEdit.Text('p');
    region.attach(element);
    root = new ContentEdit.Root.get();
    root.startDragging(element, 0, 0);
    expect(root.dragging()).toBe(element);
    cssClasses = element.domElement().getAttribute('class').split(' ');
    expect(cssClasses.indexOf('ce-element--dragging') > -1).toBe(true);
    cssClasses = document.body.getAttribute('class').split(' ');
    expect(cssClasses.indexOf('ce--dragging') > -1).toBe(true);
    expect(root._draggingDOMElement).not.toBe(null);
    root.cancelDragging();
    return region.detach(element);
  });
});

describe('`ContentEdit.Root.cancelDragging()`', function() {
  return it('should cancel a drag interaction', function() {
    var element, region, root;
    region = new ContentEdit.Region(document.createElement('div'));
    element = new ContentEdit.Element('div');
    region.attach(element);
    root = new ContentEdit.Root.get();
    if (root.dragging()) {
      root.cancelDragging();
    }
    element.drag(0, 0);
    expect(root.dragging()).toBe(element);
    root.cancelDragging();
    return expect(root.dragging()).toBe(null);
  });
});

describe('`ContentEdit.Root.resizing()`', function() {
  return it('should return the element currently being resized or null if no element is being resized', function() {
    var element, region, root;
    region = new ContentEdit.Region(document.createElement('div'));
    element = new ContentEdit.ResizableElement('div');
    region.attach(element);
    root = new ContentEdit.Root.get();
    element.resize(['top', 'left'], 0, 0);
    expect(root.resizing()).toBe(element);
    return root._onStopResizing();
  });
});

describe('`ContentEdit.Root.startResizing()`', function() {
  return it('should start a resize interaction', function() {
    var cssClasses, element, region, root;
    region = new ContentEdit.Region(document.getElementById('test'));
    element = new ContentEdit.ResizableElement('div');
    region.attach(element);
    root = new ContentEdit.Root.get();
    root.startResizing(element, ['top', 'left'], 0, 0, true);
    expect(root.resizing()).toBe(element);
    cssClasses = element.domElement().getAttribute('class').split(' ');
    expect(cssClasses.indexOf('ce-element--resizing') > -1).toBe(true);
    cssClasses = document.body.getAttribute('class').split(' ');
    expect(cssClasses.indexOf('ce--resizing') > -1).toBe(true);
    root._onStopResizing();
    return region.detach(element);
  });
});