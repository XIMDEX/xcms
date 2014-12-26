# Angular Hammer v2

An [Angular.js](https://angularjs.org/) module that enables you to bind custom behavior to [Hammer.js](http://hammerjs.github.io/) touch events. It was derived from the [Angular Hammer](https://github.com/monospaced/angular-hammer) project by [Monospaced](https://github.com/monospaced).

## Installation 

Install using the [Bower](http://bower.io/) package manager.

```bash
$ bower install ryanmullins-angular-hammer
```

Install using NPM.

```shell
$ npm install --save angular-hammer
```

Add `hmTouchEvents` to your app or module's dependencies. This module is designed to work with Angular.js v1.2.0+, and Hammer.js v2.0.0+. 

### A Note on Version Naming

Angular Hammer uses the semantic version naming convention `major.minor.patch` typical of most Bower projects, with one small difference. The `major` version will _only_ change when the major version of Hammer.js changes. Changes to `minor` should be thought of as possibly breaking, though typically that only means rewriting the values of some attributes in your HTML. Changes to `patch` should be thought of as standard bug fixes or small feature additions.

### A Note on Angular.js 2.0 

At this time Angular Hammer has been tested with both Angular.js v1.2.x and v1.3.0. Angular.js v2.0 presents massive changes to the framework. Until such time as this README indicates otherwise, it should be assumed that Angular Hammer **will not** be moving forward to Angular.js v2.0. I reserve the right to change my mind once the v2.0 spec come out and I am able to assess the transition path.

## Usage

The `hmTouchEvents` module provides a series of attribute [directives](https://docs.angularjs.org/guide/directive) for hooking into the standard Hammer.js events. The following list shows the Hammer event and corresponding Angular directive (format: &lt;eventName&gt; : &lt;directiveName&gt;). Events on the top level are fired every time a gesture of that type happens. The second-level events are more specific to the gesture state (i.e. direction, start/stop), but trigger events of their top level type. 

* pan : hmPan
    - panstart : hmPanstart
    - panmove : hmPanmove
    - panend : hmPanend
    - pancancel : hmPancancel
    - panleft : hmPanleft
    - panright : hmPanright
    - panup : hmPanup
    - pandown : hmPandown
* pinch : hmPinch
    - pinchstart : hmPinchstart
    - pinchmove : hmPinchmove
    - pinchend : hmPinchend
    - pinchcancel : hmPinchcancel
    - pinchin : hmPinchin
    - pinchout : hmPinchout
* press : hmPress
* rotate : hmRotate
    - rotatestart : hmRotatestart
    - rotatemove : hmRotatemove
    - rotateend : hmRotateend
    - rotatecancel : hmRotatecancel
* swipe : hmSwipe
    - swipeleft : hmSwipeleft
    - swiperight : hmSwiperight
    - swipeup : hmSwipeup
    - swipedown : hmSwipedown
* tap : hmTap
* doubletap : hmDoubletap 

Behaviors to be executed on an event are defined as values of the attribute. The directives first try to resolve the value to a function available on the scope; if that function is found the event is passed to it, otherwise the value is parsed as an [Angular expression](https://docs.angularjs.org/guide/expression). Beware, invalid Angular expressions will cause an error. 

```
[data-]hm-tap="onHammer"            // Using a function available in the scope
[data-]hm-tap="model.name='Ryan'"   // Using an Angular expression
```

Each element that responds to Hammer events contains it's own [manager](http://hammerjs.github.io/api/#hammer.manager), which keeps track of the various gesture [recognizers](http://hammerjs.github.io/api/#hammer.recognizer) attached to that element. Angular Hammer does not make use of the standard [Hammer() constructor](http://hammerjs.github.io/api/#hammer), instead instantiating an empty manager and adding only the required recognizers. However, if you were to add the same series of directives to an element, the default behavior would be the same as if they had been instantiated using the Hammer() constructor. 

Hammer.js provides specific options for both managers and recognizers. These options can be set in Angular Hammer using two attributes, `hmManagerOptions` and `hmRecognizerOptions`. The values for each of these attributes should be stringified JSON. Manager options are expressed as an Object. Recognizer options can be either a single Object applied to all recognizers, or an array of objects that each have a `type` property that is used to match the options to the recognizers.

Some Hammer recognizers that accept a `direction` option. For these recognizers, use the `directions` option to specify which directions you would like to support. The value of this property should be a string of `DIRECTION_*` values separated by a `|` and containing no spaces. Angular Hammer will parse this field into the proper Hammer value, and pass set the `direction` option for the recognizer.  

Example: Setting Manager Options 

```
[data-]hm-manager-options='{"touchAction":"auto","domEvents":false}' 
```

Example: Setting Recognizer Options

```
[data-]hm-recognizer-options='{"enable":true,}'     // Using an Object definition
[data-]hm-recognizer-options='[
    {"type":"tap","enable":false,},
    {"type":"pan","directions":"DIRECTION_HORIZONTAL|DIRECTION_UP"}
]'
```

### Custom Gesture Recognizers 

You can add custom gesture recognizers using the `hmCustom` directive. The behavior that is executed when this gesture is recognized is the value of this attribute. Currently (as of v2.1), only a single behavior handling function can be passed as the custom directive value. This decision was made to unify the use of the `hmManagerOptions` and `hmRecognizerOptions`, but may be changed in future versions to support multiple behavior handlers.

To actually define the custom gesture, use a modify the value of the `hmRecognizerOptions` attribute. This value may include any of the properties of the [Pan](http://hammerjs.github.io/recognizer-pan), [Pinch](http://hammerjs.github.io/recognizer-pinch), [Press](http://hammerjs.github.io/recognizer-press), [Rotate](http://hammerjs.github.io/recognizer-rotate), [Swipe](http://hammerjs.github.io/recognizer-swipe), or [Tap](http://hammerjs.github.io/recognizer-tap) gesture recognizers. The modification is the inclusion of two extra properties, `type` and `event`. The `event` property is the name of the gesture as well as the name of the event that Hammer will fire. The `type` property is used to determine which base type of gesture recognizer to modify.

I do not recommend mixing custom and standard gesture recognizers as there is a chance that conflicts may happen.

Example: Defining a Custom Gesture Recognizer 

```
[data-]hm-custom="onHammer"
hm-manager-options='{}'
hm-recognizer-options='[
  {"type":"pan","event":"twoFingerPan","pointers":2,"directions":"DIRECTION_ALL"},
  {"type":"tap","event":"dbltap","pointers":2,"taps":1}
]'
```

## Demo


* [Using the default set of recognizers](http://ryanmullins.github.io/angular-hammer/examples/default)
* [Defining a custom gesture recognizer](http://ryanmullins.github.io/angular-hammer/examples/custom).
* [Dragging a div around on the screen](http://ryanmullins.github.io/angular-hammer/examples/dragging).
