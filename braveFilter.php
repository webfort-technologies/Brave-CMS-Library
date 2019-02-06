<div id="app" class="mx-5 my-4">
  
<form action="">
    <div class="d-flex">
      <div class="py-2">
        <span v-if="syntaxStatus=='correct' && queryComplete=='yes'"
        title="Query Correct"
        ><i class="text-success fas fa-check-circle"></i></span>
        <span v-if="syntaxStatus=='error'" title="Query has error"><i class="text-danger fas fa-times-circle"></i></span>
        <span v-if="syntaxStatus!='error'  && queryComplete=='no'" title="Query Incomplete"><i class="text-warning fas fa-exclamation-circle"></i></span>
       </div>
      <div class="px-2 flex-grow-1">
        
       <editable
      name ="filter_raw"
      id="filterQuery" 
      @keydown.prevent.stop.enter="insertKeyWord()" 
      @keydown.up.prevent.stop="arrowUp()" 
      @keydown.down.prevent.stop="arrowDown()" 
      @keydown="anykeyPress()" 
      @keyup="onKeyUp($event)" 
      @blur="onBlur()"
      @focus="onFocus()"
      class="form-control form-control-sm editable-filter-query mb-2" rows="2" 
      v-model="realQuery" 
      id="exampleFormControlTextarea1" rows="2"></editable>
      </div>
      
      <div>
         <input type="hidden" name="filter" v-model="sqlQuery">
            <button class="btn btn-sm btn-primary" type="submit">Go</button>
      </div>

    </div>

    

    
 
<!-- 
  <textarea
      name ="filter_raw"
      id="filterQuery" 
      @keydown.prevent.stop.enter="insertKeyWord()" 
      @keydown.up.prevent.stop="arrowUp()" 
      @keydown.down.prevent.stop="arrowDown()" 
      @keydown="anykeyPress()" 
      @blur="onBlur()"
      @focus="onFocus()"
      class="form-control filterQuery2 mb-2" rows="2" 
      v-model="realQuery"
      id="exampleFormControlTextarea1" rows="2"></textarea> -->
  
</form>
      
  <div v-show="suggestionAr.length" class="auto-suggester" id="auto_suggester">
    <div v-for="(item,index) in suggestionAr" class="s-item" @mouseover="selectedIndex=index" @click="insertKeyWord()"  v-bind:class="{ active: isMatchItem(index) }" ><div v-html="item.name"></div>
    <div ><small>{{item.hint}}</small></div>
    </div>
  </div>

  <!-- <div class="py-3">
    <div class="d-inline" v-html="sqlQuery"></div>
  </div> -->
</div>
<script>
   var filterRaw = "";
   <?php 
        if(isset($_GET["filter_raw"]))
        {
          ?>filterRaw =<?php
          echo json_encode(utf8_encode(trim($_GET["filter_raw"]))).";"; 
        }
       ?>
</script>
