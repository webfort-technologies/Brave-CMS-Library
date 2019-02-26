/**
 * returns x, y coordinates for absolute positioning of a span within a given text input
 * at a given selection point
 * @param {object} input - the input element to obtain coordinates for
 * @param {number} selectionPoint - the selection point for the input
 */


function stripHtml(html){
    // Create a new div element
    var temporalDivElement = document.createElement("div");
    // Set the HTML content with the providen
    temporalDivElement.innerHTML = html;
    // Retrieve the text property of the element (cross-browser support)
    return temporalDivElement.textContent || temporalDivElement.innerText || "";
}




const getCursorXY = (input, selectionPoint) => {
  const {
    offsetLeft: inputX,
    offsetTop: inputY,
  } = input
  // create a dummy element that will be a clone of our input
  const div = document.createElement('div')
  // get the computed style of the input and clone it onto the dummy element
  const copyStyle = getComputedStyle(input)
  for (const prop of copyStyle) {
    div.style[prop] = copyStyle[prop]
  }
  // we need a character that will replace whitespace when filling our dummy element if it's a single line <input/>
  const swap = '.'
  const inputValue = input.tagName === 'INPUT' ? input.value.replace(/ /g, swap) : input.value
  // set the div content to that of the textarea up until selection
  const textContent = inputValue.substr(0, selectionPoint)
  // set the text content of the dummy element div
  div.textContent = textContent
  if (input.tagName === 'TEXTAREA') div.style.height = 'auto'
  // if a single line input then the div needs to be single line and not break out like a text area
  if (input.tagName === 'INPUT') div.style.width = 'auto'
  // create a marker element to obtain caret position
  const span = document.createElement('span')
  // give the span the textContent of remaining content so that the recreated dummy element is as close as possible
  span.textContent = inputValue.substr(selectionPoint) || '.'
  // append the span marker to the div
  div.appendChild(span)
  // append the dummy element to the body
  document.body.appendChild(div)
  // get the marker position, this is the caret position top and left relative to the input
  const { offsetLeft: spanX, offsetTop: spanY } = span
  // lastly, remove that dummy element
  // NOTE:: can comment this out for debugging purposes if you want to see where that span is rendered
  document.body.removeChild(div)
  // return an object with the x and y of the caret. account for input positioning so that you don't need to wrap the input
  return {
    x: inputX + spanX,
    y: inputY + spanY,
  }
}


function getSelectionCoords() {
    var sel = document.selection, range, rect;
    var x = 0, y = 0;
    if (sel) {
        if (sel.type != "Control") {
            range = sel.createRange();
            range.collapse(true);
            x = range.boundingLeft;
            y = range.boundingTop;
        }
    } else if (window.getSelection) {
        sel = window.getSelection();
        if (sel.rangeCount) {
            range = sel.getRangeAt(0).cloneRange();
            if (range.getClientRects) {
                range.collapse(true);
                if (range.getClientRects().length>0){
                    rect = range.getClientRects()[0];
                    x = rect.left;
                    y = rect.top;
                }
            }
            // Fall back to inserting a temporary element
            if (x == 0 && y == 0) {
                var span = document.createElement("span");
                if (span.getClientRects) {
                    // Ensure span has dimensions and position by
                    // adding a zero-width space character
                    span.appendChild( document.createTextNode("\u200b") );
                    range.insertNode(span);
                    rect = span.getClientRects()[0];
                    x = rect.left;
                    y = rect.top;
                    var spanParent = span.parentNode;
                    spanParent.removeChild(span);

                    // Glue any broken text nodes back together
                    spanParent.normalize();
                }
            }
        }
    }
    return { x: x, y: y };
}


    var app = new Vue({
      el    : '#app',
      data  : 
      {
        fields                : listingFields,
        
        queryOperators        : {
                                  "string"      : [ "=" , "~" ,"in"],
                                  "date"        : [ "=" , ">", "<", ">=", "<=" ],
                                  "reference"   : [ "=","!="],
                                },
        dateSuggestions        : [
                                  /*
                                    (+/-)nn(y|M|w|d|h|m)
                                    endOfDay("+1") // due by the end of tomorrow:
                                    endOfMonth("+15d") // Find issues due by the 15th of next month:
                                  */          
                                    {name:"endOfDay()", hint:"endOfDay(inc), inc is an optional increment of (+/-)(year|month|week|day|hour|minute)"},
                                    {name:"endOfMonth()", hint:""},
                                    {name:"endOfWeek()", hint:""},
                                    {name:"endOfYear()", hint:""},
                                    {name:"endOfDay()", hint:""},
                                    {name:"startOfDay()", hint:""},
                                    {name:"startOfMonth()", hint:""},
                                    {name:"startOfWeek()", hint:""},
                                    {name:"startOfYear()", hint:""},
                                    {name:"now()", hint:""},                                  
                                    {name:"datestring()", hint:""},                                  
                                    {name:"endOfDay('+1 day')", hint:"Due End of Tomorrow"},                                  
                                    {name:"endOfMonth('+15 days')", hint:"Due by the 15th of next month"},
                                ],
        keywords              : ["and","or"],
        switch                : "field", // field, operator, value, keyword.
        realQuery             : filterRaw,
        syntaxStatus          : "correct",
        queryElements         : [],
        queryComplete         : "", // yes, no
        syntaxString          : "",
        suggestionAr          : [],
        selectedIndex         : 0,
        sqlQuery              : ""

      },

      watch: {
                realQuery: function (val) 
                {


  // Replacting the spaces inside the quotes so that we do not break them apart. 
/*            

                  var phrase = val; 
                  
                  var repPharse = phrase.replace(/(<([^>]+)>)/ig,"");
                  //console.log(phrase + "90")
                  repPharse = repPharse.replace(/&nbsp;/gi, " "); 
                  var repPh2 = repPharse.replace(/(\".*?\")/gi, this.replacer); 
                  // Exploading the phrase into array where we find the spaces. 
                  console.log("Starts");
                  console.log(repPh2);
                  console.log(repPh2.split(" "));
                  console.log("ends");
                  this.queryElements = repPh2.split(" "); 

                  this.checkOrder(); 
                  this.anykeyPress();

                  console.log(this.realQuery) 
                  console.log(this.syntaxString);

*/

               /*   // Replacting the spaces inside the quotes so that we do not break them apart. 
                  //this.realQuery = this.realQuery.replace(/&nbsp;/gi, " "); 
                 // console.log("Superb phrase");
                  //console.log(val+"90"); 
                  //var phrase = val.replace(/&nbsp;/gi, " "); 
                 // var phrase = stripHtml(phrase); 
                  var phrase = val.replace(/(\".*?\")/gi, this.replacer); 
                  // Exploading the phrase into array where we find the spaces. 
                  //console.log(phrase+"90"); 
                  this.queryElements = phrase.split(" "); 
                  this.checkOrder(); 
                  if(this.realQuery!=this.syntaxString)
                  {
                    console.log("matching");
                    console.log("-"+this.realQuery+"-");
                    console.log("-"+this.syntaxString+"-");
                   // this.realQuery = this.syntaxString;
                  }
                  
                  this.anykeyPress();*/
                }
      },


      updated : function(){
        this.anykeyPress();
      },

      methods: 
      {

        onKeyUp : function(event)
        {
                  if(event.key=="ArrowDown" || event.key=="ArrowUp")
                  {
                    return; 
                  }
                  console.log(event);
                  var phrase = this.realQuery; 
                  
                  var repPharse = phrase.replace(/(<([^>]+)>)/ig,"");
                  //console.log(phrase + "90")
                  repPharse = repPharse.replace(/&nbsp;/gi, " ");  
                  
                  if(repPharse.trim()!="")
                  {
                      repPharse = repPharse.replace(/ +/gi, function()
                      {
                        return "###"; 
                      });  
                      var repPh2 = repPharse.replace(/(\".*?\")/gi, this.replacer); 
                      // Exploading the phrase into array where we find the spaces. 
                      console.log("Starts");
                      console.log(repPh2+"------");
                      console.log(repPh2.split("###"));
                      console.log("ends");
                      
                        this.queryElements = repPh2.split("###"); 
                      

                      console.log(this.queryElements);
                      

                      this.checkOrder(); 
                      this.anykeyPress();
                      this.realQuery = this.syntaxString;

                  }
                  else
                  {
                    this.queryElements =[]; 
                  }

        },



        onFocus : function()
        {  
            if(this.suggestionAr.length)
            {
              $(".auto-suggester").show(); 
            }
            $(document.body).on('click.menuHide', function(e){
              var $body = $(this); 
              if ($(e.target).parents('.auto-suggester').length==0 && !$(e.target).hasClass('auto-suggester') && !$(e.target).hasClass('filterQuery')) 
              {
                $(".auto-suggester").hide();
                $body.off('click.menuHide');
              }
            });

            this.checkOrder(); 
                      this.anykeyPress();
                      this.realQuery = this.syntaxString;

                      alert("adfad");




        },
        onBlur : function()
        {
          //setTimeout(function(){ $(".auto-suggester").hide(); }, 1000);  
        },
        anykeyPress : function()
        {
/*          var input = $(".editable-filter-query")[0]; 
          

          const {
                  offsetLeft,
                  offsetTop,
                  offsetHeight,
                  offsetWidth,
                  scrollLeft,
                  scrollTop,
                  selectionEnd,
                } = input

           const { lineHeight, paddingRight } = getComputedStyle(input);
           const { x, y } =  getSelectionCoords();
          // set the marker positioning
          // for the left positioning we ensure that the maximum left position is the width of the input minus the right padding using Math.min
          // we also account for current scroll position of the input
          const newLeft = Math.min(
            x - scrollLeft,
            (offsetLeft + offsetWidth) - parseInt(paddingRight, 10)
          )
          // for the top positioning we ensure that the maximum top position is the height of the input minus line height
          // we also account for current scroll position of the input
          const newTop = Math.min(
            y - scrollTop,
            (offsetTop + offsetHeight) - parseInt(lineHeight, 10)
          )



*/
          var coordinates2 = getSelectionCoords()
          var newLeft2 = coordinates2.x;
          var newTop2 = coordinates2.y + 30;
           $(".auto-suggester").css("left",newLeft2);
           $(".auto-suggester").css("top",newTop2);

          var target = $(".s-item.active")[0];
          if(target!= undefined )
          {
            $(".s-item.active")[0].scrollIntoView({behavior: "auto", block: "nearest"});
          }

        },

        arrowDown : function()
        {
          if(this.suggestionAr.length-1>this.selectedIndex)
          {
            this.selectedIndex++
          }
        },
        arrowUp : function()
        {
          if(this.selectedIndex>0)
          {
            this.selectedIndex--;
          }
        },
        insertKeyWord : function(event)
        {
          if(this.suggestionAr.length)
          {
            this.queryElements[this.queryElements.length-1] = this.suggestionAr[this.selectedIndex].key + "&nbsp;"; 
            this.realQuery = this.queryElements.join(" ");
            this.realQuery = this.realQuery.replace("__"," ");
            this.anykeyPress();
          }
        },

        isMatchItem : function(index){
          return index==this.selectedIndex;
        },

        parsePhase: function() 
        {
          var phrase = this.realQuery; 
          var phrase = phrase.replace(/(\".*?\")/gi, this.replacer); 
        },

        replacer : function(match, offset, string)
        {
          return match.replace(new RegExp(' ', 'g'),"__"); 

        },
        checkOrder : function()
        {
          console.log("hello");
          console.log(this.queryElements);
          console.log("hello-ends");
          this.selectedIndex = 0; 
          var syntaxStringAr = [];
          this.syntaxString   = ""
          this.sqlQuery       = ""
          this.syntaxStatus   = "correct"; 
          var order           = ["fields","operators","value","keywords"];
          var orderMarker     = 0; 
          var error           = 0; 
          var last_type       = "";
          this.queryComplete  = "yes"; 
          var referenceAr = []; 
          
          for(var i=0; i<this.queryElements.length;i++)
          {
            var alternateValue  = ""; 
            var currentElement  = this.queryElements[i];
            var expectedType    =  order[orderMarker]; 
            
            
            if(currentElement=="")
            {
              syntaxStringAr.push("&nbsp;");
            }
            if(currentElement=="")
            {
              continue; 
            }

            if(currentElement==")"|| currentElement=="(")
            {
              this.syntaxString += currentElement + " ";
              continue; 
            }

            if(expectedType=="fields")
            {
              var fieldMatches = this.fields.filter(function(item){
                                    return item.key == currentElement; 
                                 }); 
              if(fieldMatches.length>0)
              {
                  last_type = fieldMatches[0].fieldType;
                  if(last_type=="single_reference")
                  {
                     last_type="reference"
                  }
                  if(last_type=="number" || last_type=="textarea")
                  {
                     last_type="string"
                  }
                  if(last_type=="datepicker")
                  {
                     last_type="date"
                  }

                  if(last_type=="reference")
                  {
                     referenceAr = fieldMatches[0].suggestions;
                     alternateValue = currentElement+ "";
                  }
              }
            }

            if(expectedType=="operators")
            {
              if(last_type!="")
              { 
                var fieldMatches = this.queryOperators[last_type].filter(function(item){
                                    return item== currentElement; 
                                 }) 
              }
            }

            if(expectedType=="keywords")
            {
                 var fieldMatches = this.keywords.filter(function(item){
                                    return item== currentElement; 
                                 }) 
            }

            if(expectedType=="value")
            {
              if(last_type=="reference")
              {
                  /* Finding the the Key */
                  var nameToCheck = currentElement.replace(new RegExp("__", 'g')," ").replace(new RegExp('"', 'g'),"")
                  console.log("Our current element");
                  console.log(this.suggestionAr);
                  console.log(nameToCheck);
                  var suggestionAr = referenceAr;

                  var fieldMatches = suggestionAr.filter(function(item){
                                        return item.name== nameToCheck;   
                                    });

                  if(fieldMatches.length>0)
                  {
                    alternateValue = fieldMatches[0].id; 
                  }
              }
            }




            if(fieldMatches.length>0)
            {
                console.log("Match Found");
                this.syntaxString +=  currentElement  ;
                syntaxStringAr.push(currentElement);
                if(alternateValue=="")
                {
                  this.sqlQuery += currentElement + " ";
                }
                else
                {
                  this.sqlQuery += alternateValue + " ";
                }
            }
            else
            {
              this.syntaxString += "<span class='text-danger'>"+currentElement+ "</span>";
              syntaxStringAr.push("<span class='text-danger'>"+currentElement+ "</span>");
                console.log("No match found"); 
                this.syntaxStatus = "error"; 
            }
              
            orderMarker++; 
            /* ----------------------------------------*/
            /* Reseting the Order back to expect Field*/
            /* -----------------------------------------*/
            if(orderMarker==order.length)
            {
              orderMarker=0;  
            }
          }


          /* ========================================*/
         this.syntaxString = syntaxStringAr.join("&nbsp;");
         console.log(this.syntaxString)
         this.syntaxString = this.syntaxString.replace(new RegExp("&nbsp;&nbsp;","g"),"&nbsp;");
         console.log("okasy")
         console.log(syntaxStringAr)
         console.log(this.syntaxString)
          /* ========================================*/

          /* ----------------------------------------*/
          /* If query is complete */
          /* -----------------------------------------*/
            
          if(order[orderMarker-1]!="value" && this.queryElements.length>0)
          {
            this.queryComplete = "no"; 
          }
          /* -----------------------------------------*/



          this.suggestionAr=[];

          /* --------------------------------------------------------*/
          /* AUTOCOMPLETE BASED ON TYPE OF FIELD. */
          /* --------------------------------------------------------*/


          if(currentElement.trim()=="" || currentElement.trim()==")" || currentElement.trim()=="(")
          {
            orderMarker=orderMarker+1;            
          }
          
          if(order[orderMarker-1]=="fields")
          {
            this.suggestionAr = this.fields.map(function(item){
              return {name:item.name,key:item.key + " ",hint:""}; 
            })
          }

          if(order[orderMarker-1]=="operators")
          {
            if(last_type!="")
            {
              this.suggestionAr = this.queryOperators[last_type].map(function(item){
                return {name:item,key:item,hint:"" }; 
              })
            }
          }

          if(order[orderMarker-1]=="keywords")
          {
            this.suggestionAr = this.keywords.map(function(item){
              return {name:item,key:item+" ",hint:""}; 
            })
          }

          if(order[orderMarker-1]=="value")
          {
            if(last_type=="date")
              { 
                 this.suggestionAr = this.dateSuggestions.map(function(item){
                    return {name:item.name,key:item.name+" ",hint:item.hint}; 
                  })  
              }
              if(last_type=="reference")
              { 
                 this.suggestionAr = referenceAr.map(function(item){
                    return {id:item.id, name:item.name,key:'"'+item.name+'" ',hint:""}; 
                  })  
              }

          }

          /* ---------------------------------------------------------*/
          /* FILTERING WITH FUES. */
          /* ---------------------------------------------------------*/
          var options = {
            shouldSort: true,
            threshold: 0.6,
            location: 0,
            distance: 100,
            maxPatternLength: 32,
            minMatchCharLength: 1,
            keys: ["name"]
          };
          var fuse = new Fuse(this.suggestionAr, options); // "list" is the item array
          if(currentElement.trim()!="")
          {
            this.suggestionAr = fuse.search(currentElement); 
          }
          
          this.anykeyPress();
        }
      }
    })


  $(window).scroll(function(){
    app.anykeyPress();
  })

  