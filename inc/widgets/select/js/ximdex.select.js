$(function() {
    // the widget definition, where "custom" is the namespace,
    // "colorize" the widget name
    $.widget( "ximdex.inputSelect", {
      // default options
      options:{
        direction:"vertical",
        collapsable: true,
        change: null,
        showOptionText:false
      },


      // the constructor function
      _create: function() {

        this.options.collapsable = this.element.hasClass("collapsable")? true : false;
        this.options.direction = this.element.hasClass("horizontal")? "horizontal" : "vertical";

        //Creating divElements from select input
        this.xOptionElements = [];
        this.showOptions = {
                            expand:{},
                            collapse:{}
        };
        this._createNewElements();
        this.inputName = this.element.attr("name");
        this._createShowOptions();

        if (this.options.collapsable){
         /* this._on(this.xInput.parent(),{
            mouseenter:"_hover",
            mouseleave:"_hover"
          });*/
        } else{
          this.xInput.hide();
          this.xInput.parent().css(this.showOptions.expand);

        }
      },

      _setOption: function(key,value){
        this.options[key]=value;
        if (key==="direction"){
          this._createShowOptions();
        }
      },

      _createShowOptions: function(){

          //if collapsable, show all options, even the current one (extraSize = 1).
          var extraSize=0;
          if (this.options.collapsable)
            extraSize = 1;


          var expandDimension;
          var collapseDimension;
          switch(this.options.direction){

            case "horizontal":

              this.showOptions.expand = {width:expandDimension};
              collapseDimension = this.xInput.width();
              this.showOptions.collapse = {width:collapseDimension};
              this.xInput.parent().removeClass("vertical");
              this.xInput.parent().addClass("horizontal");
              break;
            case "vertical":
              expandDimension = this.xInput.height()*(this.xOptionElements.length+extraSize);
              this.showOptions.expand = {height:expandDimension};
              collapseDimension = this.xInput.height();
              this.showOptions.collapse = {height:collapseDimension};
              this.xInput.parent().removeClass("horizontal");
              this.xInput.parent().addClass("vertical");
              break;
            default: break;
          }

      },

      //Called from create function to create div elements from select input
      _createNewElements : function(){
          $divContainer = this._buildContainer();

          //Creating main div
          this.xInput = $("<div/>");
          this.xInput.attr("name",this.element.attr("name"));
          this.xInput.attr("id",this.element.attr("id"));
          this.xInput.attr("style",this.element.attr("style"));
          this.xInput.removeClass("hidden");
          this.xInput.addClass("selection icon");
          $divContainer.append(this.xInput);
          $divOptionContainer = $("<div/>").addClass("options");

          var $options = this.element.children();

          for (var i=0; i < $options.length; i++){

            $newOption = this._addOption($options[i]);
            $divOptionContainer.append($newOption);
          }

          $divContainer.append($divOptionContainer);
          this.element.before($divContainer);
          this.element.hide();
      },

      _addOption: function(option){

        var i = this.xOptionElements.length;

        var $auxOption = $(option);
        var $newOption = $("<div/>")
                        .addClass(this.element.attr("name")+"-"+$auxOption.attr("value"))
                        .addClass("option icon")
                        .attr("data-option",i)
                        .attr("title",$auxOption.attr("value"))
                        .text($auxOption.text());

        this._on($newOption,{
          click: "select"
        });



        //Creating option object
        var optionObject = {
          element: $newOption,
          value: $auxOption.attr("value"),
          text:  $auxOption.text(),
          index: i
        };
        this.xOptionElements.push(optionObject);

        if ($auxOption.is(":selected"))
          this._changeOptionSelected(i);

        return $newOption;
      },

      _buildContainer: function(){

          $divContainer = $("<div/>")
                      .addClass(this.element.attr("class"));
          $divContainer.removeClass('hidden');
          return $divContainer;
      },

      //Show changes in form input
      select: function(e){
        $option = $(e.currentTarget);
        var pos = $option.attr("data-option");
        this._changeOptionSelected(pos);
        this._trigger("change",e,{value:$option.val()});
        e.stopPropagation();
      },

     /* _hover: function(e, ui) {

          console.log(this.xOptionElements);
          console.log(this.showOptions.collapse);
          if (e.originalEvent.type === "mouseenter" || e.originalEvent.type === "mouseover") {
              var height = this.xInput.height()*(this.xOptionElements.length+1);
              this.xInput.parent().stop(true,true).animate(this.showOptions.expand,200);

          } else {
            this.xInput.parent().stop(true,true).animate(this.showOptions.collapse,200);

          }
      },*/

      _changeOptionSelected: function(index){

          this.xInput.removeClass(this.xInput.attr("name")+"-"+this.xInput.attr("data-value"));
          this.xInput.addClass(this.xInput.attr("name")+"-"+this.xOptionElements[index].value);
          this.xInput.attr("data-value",this.xOptionElements[index].value);


          if (!this.options.collapsable){
            this.xInput.siblings().removeClass("selection");
            this.xOptionElements[index].element.addClass("selection");
          }
      }


    });


});
