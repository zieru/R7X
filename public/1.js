(window["webpackJsonp"] = window["webpackJsonp"] || []).push([[1],{

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/admin/ComingSoon.vue?vue&type=script&lang=js&":
/*!****************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/admin/ComingSoon.vue?vue&type=script&lang=js& ***!
  \****************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
/* harmony default export */ __webpack_exports__["default"] = ({
  name: "ComingSoon"
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/admin/caserecording/components/FileLists.vue?vue&type=script&lang=js&":
/*!****************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/admin/caserecording/components/FileLists.vue?vue&type=script&lang=js& ***!
  \****************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
/* harmony default export */ __webpack_exports__["default"] = ({
  components: {},
  mounted: function mounted() {
    console.log('pages.files.components.FileLists.vue');
  }
  /*data() {
      return {
          filter_status: null,
          loading:true,
          tableloading:true,
          headers: [
              { text: 'Judul',align: 'start', value: 'judul',sortable: true,filterable:true },
              { text: 'MSISDN', value: 'msisdn_menghubungi',sortable: false,filterable:true },
              { text: 'MSISDN', value: 'msisdn_bermasalah',  sortable: true,filterable:true },
          ],
          items: [],
          totalItems: 0,
          pagination: {
              rowsPerPage: 10
          },
           filters: {
              name: '',
              selectedGroupIds: '',
              fileGroupId: [],
              fileGroupsHolder: []
          },
           dialogs: {
              view: {
                  file: {},
                  show: false
              },
          }
      }
  },
   watch: {
       filter_status:{
          handler () {
          }
      },
      pagination: {
          handler () {
              this.loadFiles(()=>{});
          },
          deep: true,
      },
       'filters.fileGroupId':_.debounce(function(v) {
           let selected = [];
           _.each(v,(v,k)=>{
              if(v) selected.push(k);
          });
           this.filters.selectedGroupIds = selected.join(',');
      },500),
      'search':_.debounce(function(){
          const self = this;
          self.loadFiles(()=>{});
      },700),
      'filters.selectedGroupIds'(v) {
          this.loadFiles(()=>{});
      },
      'filters.name':_.debounce(function(v) {
          this.loadFiles(()=>{});
      },500),
      'pagination.page':function(){
          this.loadFiles(()=>{});
      },
      'pagination.rowsPerPage':function(){
          this.loadFiles(()=>{});
      },
      items(){
          this.loading = false
          this.tableloading = false;
      }
  },
  methods: {
      getFullUrl(file, width, action) {
           let w = width || 4000;
          let act = action || 'resize';
           return LSK_APP.APP_URL +`/files/`+file.id+`/preview?w=`+w+`&action=`+act;
      },
      downloadFile(file) {
          window.open(LSK_APP.APP_URL + '/files/'+file.id+'/download?file_token='+file.file_token);
      },
      showDialog(dialog, data) {
           const self = this;
           switch (dialog){
              case 'file_show':
                  self.dialogs.view.file = data;
                  setTimeout(()=>{
                      self.dialogs.view.show = true;
                  },500);
                  break;
          }
      },
      trash(file) {
          const self = this;
           self.$store.commit('showDialog',{
              type: "confirm",
              title: "Confirm Deletion",
              message: "Are you sure you want to delete this file?",
              okCb: ()=>{
                   axios.delete('/admin/files/' + file.id).then(function(response) {
                       self.$store.commit('showSnackbar',{
                          message: response.data.message,
                          color: 'success',
                          duration: 3000
                      });
                       self.$eventBus.$emit('FILE_DELETED');
                       // maybe the action took place from view file
                      // lets close it.
                      self.dialogs.view.show = false;
                   }).catch(function (error) {
                      if (error.response) {
                          self.$store.commit('showSnackbar',{
                              message: error.response.data.message,
                              color: 'error',
                              duration: 3000
                          });
                      } else if (error.request) {
                          console.log(error.request);
                       } else {
                          console.log('Error', error.message);
                      }
                  });
              },
              cancelCb: ()=>{
                  console.log("CANCEL");
              }
          });
      },
      loadFileGroups(cb) {
           const self = this;
           let params = {
              paginate: 'no'
          };
           axios.get('/admin/file-groups',{params: params}).then(function(response) {
              self.filters.fileGroupsHolder = response.data.data;
              cb();
          });
      },
      loadFiles(cb) {
          const self = this;
          self.loading = true;
          let targetIndex = '';
              if(self.headers.findIndex(item => item.value === self.pagination.sortBy[0] < 0)){
                  targetIndex = '';
              }else{
                  targetIndex = self.headers.findIndex(item => item.value === self.pagination.sortBy[0]);
              }
          const direction = self.pagination.sortDesc[0] ? 'desc' : 'asc';
          let params = {
              "search[value]": self.filters.name,
              file_group_id: self.filters.selectedGroupIds,
              draw: self.pagination.page,
              length: self.pagination.itemsPerPage,
              start: (((self.pagination.page/self.pagination.itemsPerPage)*self.pagination.itemsPerPage)-1)*self.pagination.itemsPerPage,
              'order[0][dir]': direction,
              'order[0][column]' :  targetIndex
          };
          function search(nameKey, myArray){
              for (var i=0; i < myArray.length; i++) {
                  if (myArray[i].name === nameKey) {
                      return myArray[i];
                  }
              }
          }
            /!*Object.keys(self.pagination.sortBy).find(key => self.pagination.sortBy[value] === value)*!/
          $.each(self.headers, function(key, value) {
              params['columns['+key+'][data]'] = value.value;
              params['columns['+key+'][searchable]'] = value.filterable;
              params['columns['+key+'][orderable]'] = value.sortable;
          });
           (this.pagination);
            axios.get('/api/admin/caserecording',{params: params})
              .then(function(response) {
                  self.items = response.data.data;
                  self.totalItems = response.data.recordsFiltered;
                  self.pagination.totalItems = self.totalItems;
                  (cb || Function)();
               }).catch(function(error) {
                  console.log(error.response);
              if (error.response && error.response.status != 200) {
                  self.$store.commit('showSnackbar', {
                      message: error.response.status+':'+ error.response.statusText + ' Please Reload Your Browser',
                      color: 'error',
                      duration: 10000
                  });
              };
                  self.loading = false;
              self.tableloading = false;
          })
      }
  }*/

});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/admin/dashboard/Home.vue?vue&type=script&lang=js&":
/*!********************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/admin/dashboard/Home.vue?vue&type=script&lang=js& ***!
  \********************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _ComingSoon__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./../ComingSoon */ "./resources/js/admin/ComingSoon.vue");
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//

/* harmony default export */ __webpack_exports__["default"] = ({
  components: {
    'comingsoonblock': _ComingSoon__WEBPACK_IMPORTED_MODULE_0__["default"]
  },
  mounted: function mounted() {
    var self = this;
    self.$store.commit('setBreadcrumbs', [{
      label: 'Dashboard',
      name: ''
    }]);
  }
});

/***/ }),

/***/ "./node_modules/css-loader/index.js?!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/admin/ComingSoon.vue?vue&type=style&index=0&id=dd26360c&scoped=true&lang=css&":
/*!***********************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/css-loader??ref--7-1!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src??ref--7-2!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/admin/ComingSoon.vue?vue&type=style&index=0&id=dd26360c&scoped=true&lang=css& ***!
  \***********************************************************************************************************************************************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(/*! ../../../node_modules/css-loader/lib/css-base.js */ "./node_modules/css-loader/lib/css-base.js")(false);
// imports
exports.push([module.i, "@import url(https://fonts.googleapis.com/css?family=Fredoka+One);", ""]);

// module
exports.push([module.i, "\n.store-container[data-v-dd26360c] {\n    line-height:0;\n    margin: 50px auto;\n}\n.stroke[data-v-dd26360c] {\n    stroke: #0170bb;\n    stroke-width: 5;\n    stroke-linejoin: round;\n    stroke-miterlimit: 10;\n}\n.round-end[data-v-dd26360c] {\n    stroke-linecap: round;\n}\n#store[data-v-dd26360c] {\n    -webkit-animation: fadeIn-data-v-dd26360c 0.8s ease-in;\n            animation: fadeIn-data-v-dd26360c 0.8s ease-in;\n}\n.border-animation[data-v-dd26360c] {\n    background-color: white;\n    border-radius: 10px;\n    position: relative;\n}\n.border-animation[data-v-dd26360c]:after {\n    content: \"\";\n    background: linear-gradient(45deg, #ccc 48.9%, #0170bb 49%);\n    background-size: 300% 300%;\n    border-radius: 10px;\n    position: absolute;\n    top: -5px;\n    left: -5px;\n    height: calc(100% + 10px);\n    width: calc(100% + 10px);\n    z-index: -1;\n    -webkit-animation: borderGradient-data-v-dd26360c 8s linear both infinite;\n            animation: borderGradient-data-v-dd26360c 8s linear both infinite;\n}\n@-webkit-keyframes borderGradient-data-v-dd26360c {\n0%,\n    100% {\n        background-position: 0% 100%;\n}\n50% {\n        background-position: 100% 0%;\n}\n}\n@keyframes borderGradient-data-v-dd26360c {\n0%,\n    100% {\n        background-position: 0% 100%;\n}\n50% {\n        background-position: 100% 0%;\n}\n}\n@-webkit-keyframes fadeIn-data-v-dd26360c {\nto {\n        opacity: 1;\n}\n}\n@keyframes fadeIn-data-v-dd26360c {\nto {\n        opacity: 1;\n}\n}\n#browser[data-v-dd26360c] {\n    transform: translateY(-100%);\n    -webkit-animation: moveDown-data-v-dd26360c 1.5s cubic-bezier(0.77, -0.5, 0.3, 1.5) forwards;\n    animation: moveDown-data-v-dd26360c 1.5s cubic-bezier(0.77, -0.5, 0.3, 1.5) forwards;\n}\n@-webkit-keyframes moveDown-data-v-dd26360c {\nfrom {\n        transform: translate(0, -100%);\n}\nto {\n        transform: translate(0, 0);\n}\n}\n@keyframes moveDown-data-v-dd26360c {\nfrom {\n        transform: translate(0, -100%);\n}\nto {\n        transform: translate(0, 0);\n}\n}\n#toldo[data-v-dd26360c] {\n    -webkit-animation: fadeIn-data-v-dd26360c 1s 1.4s ease-in forwards;\n            animation: fadeIn-data-v-dd26360c 1s 1.4s ease-in forwards;\n}\n.grass[data-v-dd26360c] {\n    -webkit-animation: fadeIn-data-v-dd26360c 0.5s 1.6s ease-in forwards;\n            animation: fadeIn-data-v-dd26360c 0.5s 1.6s ease-in forwards;\n}\n#window[data-v-dd26360c] {\n    -webkit-animation: fadeIn-data-v-dd26360c 0.5s 1.8s ease-in forwards;\n            animation: fadeIn-data-v-dd26360c 0.5s 1.8s ease-in forwards;\n}\n#door[data-v-dd26360c] {\n    -webkit-animation: fadeIn-data-v-dd26360c 0.5s 2s ease-in forwards;\n            animation: fadeIn-data-v-dd26360c 0.5s 2s ease-in forwards;\n}\n#sign[data-v-dd26360c] {\n    transform-origin: 837px 597px;\n    -webkit-animation: pendulum-data-v-dd26360c 1.5s 2s ease-in-out alternate;\n            animation: pendulum-data-v-dd26360c 1.5s 2s ease-in-out alternate;\n}\n.trees[data-v-dd26360c] {\n    -webkit-animation: fadeIn-data-v-dd26360c 0.5s 2.2s ease-in forwards;\n            animation: fadeIn-data-v-dd26360c 0.5s 2.2s ease-in forwards;\n}\n#toldo[data-v-dd26360c],\n.grass[data-v-dd26360c],\n#window[data-v-dd26360c],\n#door[data-v-dd26360c],\n.trees[data-v-dd26360c],\n.cat[data-v-dd26360c],\n.cat-shadow[data-v-dd26360c],\n.box[data-v-dd26360c],\n.parachute[data-v-dd26360c],\n.tshirt[data-v-dd26360c],\n.cap[data-v-dd26360c],\n.ball[data-v-dd26360c],\n#text[data-v-dd26360c],\n#button[data-v-dd26360c],\n.sky-circle[data-v-dd26360c],\n.sky-circle2[data-v-dd26360c],\n.sky-circle3[data-v-dd26360c] {\n    opacity: 0;\n}\n@-webkit-keyframes pendulum-data-v-dd26360c {\n20% {\n        transform: rotate(60deg);\n}\n40% {\n        transform: rotate(-40deg);\n}\n60% {\n        transform: rotate(20deg);\n}\n80% {\n        transform: rotate(-5deg);\n}\n}\n@keyframes pendulum-data-v-dd26360c {\n20% {\n        transform: rotate(60deg);\n}\n40% {\n        transform: rotate(-40deg);\n}\n60% {\n        transform: rotate(20deg);\n}\n80% {\n        transform: rotate(-5deg);\n}\n}\n.cat[data-v-dd26360c] {\n    transform-origin: 1145px 620px;\n}\n.cat-shadow[data-v-dd26360c] {\n    transform-origin: 1115px 625px;\n}\n#store:hover .cat[data-v-dd26360c] {\n    -webkit-animation: catHi-data-v-dd26360c 3s 3s cubic-bezier(0.7, -0.5, 0.3, 1.4);\n            animation: catHi-data-v-dd26360c 3s 3s cubic-bezier(0.7, -0.5, 0.3, 1.4);\n}\n#store:hover .cat-shadow[data-v-dd26360c] {\n    -webkit-animation: catShadow-data-v-dd26360c 4s 2s cubic-bezier(0.7, -0.5, 0.3, 1.4) alternate;\n            animation: catShadow-data-v-dd26360c 4s 2s cubic-bezier(0.7, -0.5, 0.3, 1.4) alternate;\n}\n@-webkit-keyframes catHi-data-v-dd26360c {\n0%,\n    100% {\n        opacity: 0;\n        transform: scale(0.8);\n}\n10%,\n    60% {\n        transform: scale(1);\n        opacity: 1;\n}\n}\n@keyframes catHi-data-v-dd26360c {\n0%,\n    100% {\n        opacity: 0;\n        transform: scale(0.8);\n}\n10%,\n    60% {\n        transform: scale(1);\n        opacity: 1;\n}\n}\n@-webkit-keyframes catShadow-data-v-dd26360c {\n0%,\n    100% {\n        transform: translate(40px, -35px) scale(0.3);\n}\n10%,\n    60% {\n        opacity: 1;\n        transform: translate(-5px, 10px) scale(0.5);\n}\n60% {\n        opacity: 0;\n}\n}\n@keyframes catShadow-data-v-dd26360c {\n0%,\n    100% {\n        transform: translate(40px, -35px) scale(0.3);\n}\n10%,\n    60% {\n        opacity: 1;\n        transform: translate(-5px, 10px) scale(0.5);\n}\n60% {\n        opacity: 0;\n}\n}\n.box[data-v-dd26360c],\n.parachute[data-v-dd26360c] {\n    transform-origin: 430px 100px;\n    -webkit-animation: moveBox-data-v-dd26360c 14s 4s linear forwards infinite;\n            animation: moveBox-data-v-dd26360c 14s 4s linear forwards infinite;\n}\n.parachute[data-v-dd26360c] {\n    -webkit-animation: parachute-data-v-dd26360c 14s 4s linear forwards infinite;\n            animation: parachute-data-v-dd26360c 14s 4s linear forwards infinite;\n}\n@-webkit-keyframes moveBox-data-v-dd26360c {\n0% {\n        opacity: 0;\n        transform: translate(0, -150px) rotate(20deg);\n}\n15% {\n        opacity: 1;\n        transform: translate(0, 100px) rotate(-15deg);\n}\n25% {\n        transform: translate(0, 250px) rotate(10deg);\n}\n30% {\n        transform: translate(0, 350px) rotate(-5deg);\n}\n35% {\n        opacity: 1;\n        transform: translate(0, 570px) rotate(0deg);\n}\n45%,\n    100% {\n        opacity: 0;\n        transform: translate(0, 570px);\n}\n}\n@keyframes moveBox-data-v-dd26360c {\n0% {\n        opacity: 0;\n        transform: translate(0, -150px) rotate(20deg);\n}\n15% {\n        opacity: 1;\n        transform: translate(0, 100px) rotate(-15deg);\n}\n25% {\n        transform: translate(0, 250px) rotate(10deg);\n}\n30% {\n        transform: translate(0, 350px) rotate(-5deg);\n}\n35% {\n        opacity: 1;\n        transform: translate(0, 570px) rotate(0deg);\n}\n45%,\n    100% {\n        opacity: 0;\n        transform: translate(0, 570px);\n}\n}\n@-webkit-keyframes parachute-data-v-dd26360c {\n0% {\n        transform: translate(0, -150px) rotate(20deg) scale(0.8);\n        opacity: 0;\n}\n15% {\n        transform: translate(0, 100px) rotate(-15deg) scale(1);\n        opacity: 1;\n}\n25% {\n        transform: translate(0, 250px) rotate(10deg);\n}\n30% {\n        transform: translate(0, 350px) rotate(-5deg);\n}\n33% {\n        transform: translate(0, 460px) rotate(0deg) scale(0.9);\n        opacity: 1;\n}\n45%,\n    100% {\n        transform: translate(0, 480px);\n        opacity: 0;\n}\n}\n@keyframes parachute-data-v-dd26360c {\n0% {\n        transform: translate(0, -150px) rotate(20deg) scale(0.8);\n        opacity: 0;\n}\n15% {\n        transform: translate(0, 100px) rotate(-15deg) scale(1);\n        opacity: 1;\n}\n25% {\n        transform: translate(0, 250px) rotate(10deg);\n}\n30% {\n        transform: translate(0, 350px) rotate(-5deg);\n}\n33% {\n        transform: translate(0, 460px) rotate(0deg) scale(0.9);\n        opacity: 1;\n}\n45%,\n    100% {\n        transform: translate(0, 480px);\n        opacity: 0;\n}\n}\n.tshirt[data-v-dd26360c] {\n    -webkit-animation: fadeInOut-data-v-dd26360c 42s 10s ease-in forwards infinite;\n            animation: fadeInOut-data-v-dd26360c 42s 10s ease-in forwards infinite;\n}\n.cap[data-v-dd26360c] {\n    -webkit-animation: fadeInOut-data-v-dd26360c 42s 24s ease-in forwards infinite;\n            animation: fadeInOut-data-v-dd26360c 42s 24s ease-in forwards infinite;\n}\n.ball[data-v-dd26360c] {\n    -webkit-animation: fadeInOut-data-v-dd26360c 42s 38s ease-in forwards infinite;\n            animation: fadeInOut-data-v-dd26360c 42s 38s ease-in forwards infinite;\n}\n#text[data-v-dd26360c],\n#button[data-v-dd26360c] {\n    -webkit-animation: fadeIn-data-v-dd26360c 1s 5s ease-in forwards;\n            animation: fadeIn-data-v-dd26360c 1s 5s ease-in forwards;\n}\n@-webkit-keyframes fadeInOut-data-v-dd26360c {\n5%,\n    12% {\n        opacity: 1;\n}\n20% {\n        opacity: 0;\n}\n}\n@keyframes fadeInOut-data-v-dd26360c {\n5%,\n    12% {\n        opacity: 1;\n}\n20% {\n        opacity: 0;\n}\n}\n.cloud[data-v-dd26360c] {\n    -webkit-animation: clouds-data-v-dd26360c 50s linear backwards infinite;\n            animation: clouds-data-v-dd26360c 50s linear backwards infinite;\n}\n.cloud2[data-v-dd26360c] {\n    -webkit-animation: clouds-data-v-dd26360c 40s 40s linear backwards infinite;\n            animation: clouds-data-v-dd26360c 40s 40s linear backwards infinite;\n}\n.plane[data-v-dd26360c] {\n    -webkit-animation: clouds-data-v-dd26360c 30s linear backwards infinite;\n            animation: clouds-data-v-dd26360c 30s linear backwards infinite;\n    will-change: transform;\n}\n@-webkit-keyframes clouds-data-v-dd26360c {\nfrom {\n        transform: translate(-150%, 0);\n}\nto {\n        transform: translate(150%, 0);\n}\n}\n@keyframes clouds-data-v-dd26360c {\nfrom {\n        transform: translate(-150%, 0);\n}\nto {\n        transform: translate(150%, 0);\n}\n}\n.sky-circle[data-v-dd26360c] {\n    -webkit-animation: fadeInOut-data-v-dd26360c 10s 5s ease-in infinite;\n            animation: fadeInOut-data-v-dd26360c 10s 5s ease-in infinite;\n}\n.sky-circle2[data-v-dd26360c] {\n    -webkit-animation: fadeInOut-data-v-dd26360c 12s 30s ease-in infinite;\n            animation: fadeInOut-data-v-dd26360c 12s 30s ease-in infinite;\n}\n.sky-circle3[data-v-dd26360c] {\n    -webkit-animation: fadeInOut-data-v-dd26360c 8s 40s ease-in infinite;\n            animation: fadeInOut-data-v-dd26360c 8s 40s ease-in infinite;\n}\n\n", ""]);

// exports


/***/ }),

/***/ "./node_modules/css-loader/index.js?!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/admin/caserecording/components/FileLists.vue?vue&type=style&index=0&id=3398ee6c&scoped=true&lang=css&":
/*!***********************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/css-loader??ref--7-1!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src??ref--7-2!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/admin/caserecording/components/FileLists.vue?vue&type=style&index=0&id=3398ee6c&scoped=true&lang=css& ***!
  \***********************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(/*! ../../../../../node_modules/css-loader/lib/css-base.js */ "./node_modules/css-loader/lib/css-base.js")(false);
// imports


// module
exports.push([module.i, "\n.mytable th[data-v-3398ee6c] {\n    font-weight:bolder;\n}\n.file_view_popup[data-v-3398ee6c] {\n    min-width: 500px;\n    text-align: center;\n}\n", ""]);

// exports


/***/ }),

/***/ "./node_modules/style-loader/index.js!./node_modules/css-loader/index.js?!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/admin/ComingSoon.vue?vue&type=style&index=0&id=dd26360c&scoped=true&lang=css&":
/*!***************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/style-loader!./node_modules/css-loader??ref--7-1!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src??ref--7-2!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/admin/ComingSoon.vue?vue&type=style&index=0&id=dd26360c&scoped=true&lang=css& ***!
  \***************************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {


var content = __webpack_require__(/*! !../../../node_modules/css-loader??ref--7-1!../../../node_modules/vue-loader/lib/loaders/stylePostLoader.js!../../../node_modules/postcss-loader/src??ref--7-2!../../../node_modules/vue-loader/lib??vue-loader-options!./ComingSoon.vue?vue&type=style&index=0&id=dd26360c&scoped=true&lang=css& */ "./node_modules/css-loader/index.js?!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/admin/ComingSoon.vue?vue&type=style&index=0&id=dd26360c&scoped=true&lang=css&");

if(typeof content === 'string') content = [[module.i, content, '']];

var transform;
var insertInto;



var options = {"hmr":true}

options.transform = transform
options.insertInto = undefined;

var update = __webpack_require__(/*! ../../../node_modules/style-loader/lib/addStyles.js */ "./node_modules/style-loader/lib/addStyles.js")(content, options);

if(content.locals) module.exports = content.locals;

if(false) {}

/***/ }),

/***/ "./node_modules/style-loader/index.js!./node_modules/css-loader/index.js?!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/admin/caserecording/components/FileLists.vue?vue&type=style&index=0&id=3398ee6c&scoped=true&lang=css&":
/*!***************************************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/style-loader!./node_modules/css-loader??ref--7-1!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src??ref--7-2!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/admin/caserecording/components/FileLists.vue?vue&type=style&index=0&id=3398ee6c&scoped=true&lang=css& ***!
  \***************************************************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {


var content = __webpack_require__(/*! !../../../../../node_modules/css-loader??ref--7-1!../../../../../node_modules/vue-loader/lib/loaders/stylePostLoader.js!../../../../../node_modules/postcss-loader/src??ref--7-2!../../../../../node_modules/vue-loader/lib??vue-loader-options!./FileLists.vue?vue&type=style&index=0&id=3398ee6c&scoped=true&lang=css& */ "./node_modules/css-loader/index.js?!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/admin/caserecording/components/FileLists.vue?vue&type=style&index=0&id=3398ee6c&scoped=true&lang=css&");

if(typeof content === 'string') content = [[module.i, content, '']];

var transform;
var insertInto;



var options = {"hmr":true}

options.transform = transform
options.insertInto = undefined;

var update = __webpack_require__(/*! ../../../../../node_modules/style-loader/lib/addStyles.js */ "./node_modules/style-loader/lib/addStyles.js")(content, options);

if(content.locals) module.exports = content.locals;

if(false) {}

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/admin/ComingSoon.vue?vue&type=template&id=dd26360c&scoped=true&":
/*!********************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/admin/ComingSoon.vue?vue&type=template&id=dd26360c&scoped=true& ***!
  \********************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "render", function() { return render; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return staticRenderFns; });
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c("div", [
    _c("div", { staticClass: "store-container" }, [
      _c("div", { staticClass: "border-animation" }, [
        _c(
          "svg",
          {
            attrs: {
              xmlns: "http://www.w3.org/2000/svg",
              id: "store",
              viewBox: "130 0 1230 930"
            }
          },
          [
            _c("defs", [
              _c(
                "filter",
                { attrs: { id: "f1" } },
                [
                  _c("feGaussianBlur", {
                    attrs: { in: "SourceGraphic", stdDeviation: "0,4" }
                  })
                ],
                1
              ),
              _vm._v(" "),
              _c("circle", {
                staticClass: "stroke",
                attrs: {
                  id: "sky-circle",
                  fill: "none",
                  cx: "198.7",
                  cy: "314",
                  r: "5.5"
                }
              }),
              _vm._v(" "),
              _c("path", {
                staticClass: "stroke",
                attrs: {
                  id: "cloud",
                  fill: "#FFF",
                  d:
                    "M503.6 39.1c-2.9 0.2-5.8 0.7-8.5 1.4 -14.7-24.5-42.3-40-72.8-37.8 -31.2 2.2-56.9 22.4-67.6 49.7 -2.5-0.4-5-0.5-7.6-0.3 -18.5 1.3-32.5 17.4-31.2 35.9s17.4 32.5 35.9 31.2c2.3-0.2 4.6-0.6 6.8-1.2 14.1 26.5 42.9 43.6 74.8 41.3 23.1-1.6 43.2-13.1 56.4-30.1 6.3 2.5 13.2 3.6 20.4 3.1 25.7-1.8 45.1-24.1 43.3-49.9C551.6 56.7 529.3 37.3 503.6 39.1z"
                }
              }),
              _vm._v(" "),
              _c("path", {
                staticClass: "stroke",
                attrs: {
                  id: "cloud2",
                  fill: "#FFF",
                  transform: "scale(.8)",
                  d:
                    "M503.6 39.1c-2.9 0.2-5.8 0.7-8.5 1.4 -14.7-24.5-42.3-40-72.8-37.8 -31.2 2.2-56.9 22.4-67.6 49.7 -2.5-0.4-5-0.5-7.6-0.3 -18.5 1.3-32.5 17.4-31.2 35.9s17.4 32.5 35.9 31.2c2.3-0.2 4.6-0.6 6.8-1.2 14.1 26.5 42.9 43.6 74.8 41.3 23.1-1.6 43.2-13.1 56.4-30.1 6.3 2.5 13.2 3.6 20.4 3.1 25.7-1.8 45.1-24.1 43.3-49.9C551.6 56.7 529.3 37.3 503.6 39.1z"
                }
              }),
              _vm._v(" "),
              _c("g", { attrs: { id: "tree" } }, [
                _c("rect", {
                  staticClass: "stroke",
                  attrs: {
                    x: "1114.2",
                    y: "721.5",
                    fill: "#FFF",
                    width: "22",
                    height: "127"
                  }
                }),
                _vm._v(" "),
                _c("g", { attrs: { opacity: "0.4" } }, [
                  _c("path", {
                    attrs: {
                      fill: "#0170BB",
                      d:
                        "M1085.2 552.4c-29.4 14.7-49.5 45-49.5 80.1 0 49.4 40.1 89.5 89.5 89.5 49.4 0 89.5-40.1 89.5-89.5 0-35.2-20.3-65.6-49.8-80.2"
                    }
                  }),
                  _vm._v(" "),
                  _c("path", {
                    attrs: {
                      fill: "#0170BB",
                      d:
                        "M1164.9 552.3c10-10.1 16.1-24 16.1-39.3 0-30.9-25.1-56-56-56s-56 25.1-56 56c0 15.4 6.2 29.3 16.2 39.4"
                    }
                  }),
                  _vm._v(" "),
                  _c("path", {
                    attrs: {
                      fill: "#0170BB",
                      d: "M1153.9 561c4-2.4 7.7-5.4 11-8.7"
                    }
                  }),
                  _vm._v(" "),
                  _c("path", {
                    attrs: {
                      fill: "#0170BB",
                      d: "M1104.3 545.5c-6.7 1.6-13.1 3.9-19.1 7"
                    }
                  })
                ]),
                _vm._v(" "),
                _c("path", {
                  staticClass: "stroke round-end",
                  attrs: {
                    fill: "none",
                    d:
                      "M1085.2 552.4c-29.4 14.7-49.5 45-49.5 80.1 0 49.4 40.1 89.5 89.5 89.5 49.4 0 89.5-40.1 89.5-89.5 0-35.2-20.3-65.6-49.8-80.2"
                  }
                }),
                _vm._v(" "),
                _c("path", {
                  staticClass: "stroke round-end",
                  attrs: {
                    fill: "none",
                    d:
                      "M1164.9 552.3c10-10.1 16.1-24 16.1-39.3 0-30.9-25.1-56-56-56s-56 25.1-56 56c0 15.4 6.2 29.3 16.2 39.4"
                  }
                }),
                _vm._v(" "),
                _c("path", {
                  staticClass: "stroke round-end",
                  attrs: { fill: "none", d: "M1153.9 561c4-2.4 7.7-5.4 11-8.7" }
                }),
                _vm._v(" "),
                _c("path", {
                  staticClass: "stroke round-end",
                  attrs: {
                    fill: "none",
                    d: "M1104.3 545.5c-6.7 1.6-13.1 3.9-19.1 7"
                  }
                })
              ]),
              _vm._v(" "),
              _c("g", { attrs: { id: "cat" } }, [
                _c("circle", {
                  attrs: { fill: "#0170BB", cx: "1115", cy: "625", r: "25" }
                }),
                _vm._v(" "),
                _c("path", {
                  attrs: {
                    fill: "#FFF",
                    stroke: "#0170BB",
                    "stroke-width": "3",
                    "stroke-linecap": "round",
                    "stroke-linejoin": "round",
                    "stroke-miterlimit": "10",
                    d: "M1097.1 612.3c0 0-4.5-9.3-0.3-17.7 0 0 4.5 5.6 9.3 7"
                  }
                }),
                _vm._v(" "),
                _c("path", {
                  attrs: {
                    fill: "#FFF",
                    stroke: "#0170BB",
                    "stroke-width": "3",
                    "stroke-linecap": "round",
                    "stroke-linejoin": "round",
                    "stroke-miterlimit": "10",
                    d: "M1140.6 612.3c0 0 4.5-9.3 0.3-17.7 0 0-4.5 5.6-9.3 7"
                  }
                }),
                _vm._v(" "),
                _c("circle", {
                  attrs: {
                    fill: "#FFF",
                    stroke: "#0170BB",
                    "stroke-width": "3",
                    "stroke-linecap": "round",
                    "stroke-linejoin": "round",
                    "stroke-miterlimit": "10",
                    cx: "1118.6",
                    cy: "621.7",
                    r: "23.1"
                  }
                }),
                _vm._v(" "),
                _c("path", {
                  attrs: {
                    fill: "#ED4F43",
                    d: "M1122.4 625c0 5.3-1.4 6.3-3.8 6.3 -2.4 0-3.8-1-3.8-6.3"
                  }
                }),
                _vm._v(" "),
                _c("path", {
                  attrs: {
                    fill: "#FFF",
                    stroke: "#0170BB",
                    "stroke-width": "3",
                    "stroke-linecap": "round",
                    "stroke-linejoin": "round",
                    "stroke-miterlimit": "10",
                    d:
                      "M1132.8 621.2c0 3.9-3.2 7-7 7s-7-3.2-7-7h-0.2c0 3.9-3.2 7-7 7s-7-3.2-7-7"
                  }
                }),
                _vm._v(" "),
                _c("path", {
                  attrs: {
                    fill: "#FFF",
                    stroke: "#0170BB",
                    "stroke-width": "3",
                    "stroke-linecap": "round",
                    "stroke-linejoin": "round",
                    "stroke-miterlimit": "10",
                    d: "M1104.7 613c0 0 0-3.1 2.8-3.8 2.9-0.8 4.2 1.7 4.2 1.7"
                  }
                }),
                _vm._v(" "),
                _c("path", {
                  attrs: {
                    fill: "#FFF",
                    stroke: "#0170BB",
                    "stroke-width": "3",
                    "stroke-linecap": "round",
                    "stroke-linejoin": "round",
                    "stroke-miterlimit": "10",
                    d: "M1132.6 613c0 0 0-3.1-2.8-3.8 -2.9-0.8-4.2 1.7-4.2 1.7"
                  }
                }),
                _vm._v(" "),
                _c("path", {
                  attrs: {
                    fill: "#0170BB",
                    d:
                      "M1118.6 622c0 0-2.9-0.8-2.9-1.9v0c0-1 0.8-1.9 1.9-1.9h2.2c1 0 1.9 0.8 1.9 1.9v0C1121.6 621.2 1118.6 622 1118.6 622z"
                  }
                })
              ]),
              _vm._v(" "),
              _c("g", { attrs: { id: "parachute" } }, [
                _c("path", {
                  attrs: {
                    fill: "#a5c7e4",
                    d:
                      "M429.4 2.5c-36.7 0-66.3 32.4-66.3 72.4 -9.3-6.7-19.4-5.9-30.1 0C333 74.9 355 2.5 429.4 2.5"
                  }
                }),
                _vm._v(" "),
                _c("path", {
                  attrs: {
                    fill: "#a5c7e4",
                    d:
                      "M429.6 2.5c36.7 0 66.3 32.4 66.3 72.4 9.3-6.7 19.4-5.9 30.1 0C526 74.9 504 2.5 429.6 2.5"
                  }
                }),
                _vm._v(" "),
                _c("path", {
                  attrs: {
                    fill: "#a5c7e4",
                    d:
                      "M429.6 2.5c15.3 0 27.6 36.5 27.7 76 -9.3-3.9-18.5-5.9-27.7-6h-0.2c-9.2 0-18.4 2.1-27.7 6 0.1-39.5 12.4-76 27.7-76"
                  }
                }),
                _vm._v(" "),
                _c("path", {
                  staticClass: "stroke",
                  attrs: {
                    fill: "none",
                    d: "M401.8 78.5c0 0-13.4-14.6-38.9-3.6"
                  }
                }),
                _vm._v(" "),
                _c("path", {
                  staticClass: "stroke",
                  attrs: {
                    fill: "none",
                    d:
                      "M429.4 2.5c-36.7 0-66.3 32.4-66.3 72.4 -9.3-6.7-19.4-5.9-30.1 0C333 74.9 355 2.5 429.4 2.5"
                  }
                }),
                _vm._v(" "),
                _c("path", {
                  staticClass: "stroke",
                  attrs: {
                    fill: "none",
                    d:
                      "M429.6 2.5c36.7 0 66.3 32.4 66.3 72.4 9.3-6.7 19.4-5.9 30.1 0C526 74.9 504 2.5 429.6 2.5"
                  }
                }),
                _vm._v(" "),
                _c("path", {
                  staticClass: "stroke",
                  attrs: {
                    fill: "none",
                    d:
                      "M429.6 2.5c15.3 0 27.6 36.5 27.7 76 -9.3-3.9-18.5-5.9-27.7-6h-0.2c-9.2 0-18.4 2.1-27.7 6 0.1-39.5 12.4-76 27.7-76"
                  }
                }),
                _vm._v(" "),
                _c("path", {
                  staticClass: "stroke",
                  attrs: {
                    fill: "none",
                    d:
                      "M362.9 75l66.6 104 66-104.1c-25.5-10.9-38.9 3.6-38.9 3.6L429.5 179 401.3 78"
                  }
                }),
                _vm._v(" "),
                _c("polyline", {
                  staticClass: "stroke",
                  attrs: {
                    fill: "none",
                    points: "333.3 75 429.5 179 526.3 75 "
                  }
                })
              ]),
              _vm._v(" "),
              _c("g", { attrs: { id: "box" } }, [
                _c("rect", {
                  staticClass: "stroke",
                  attrs: {
                    x: "356",
                    y: "47",
                    fill: "#FFF",
                    width: "106.2",
                    height: "86"
                  }
                }),
                _vm._v(" "),
                _c("polygon", {
                  staticClass: "stroke",
                  attrs: {
                    fill: "#FFF",
                    points: " 462.2 47 356 47 403.2 31 500.1 31 "
                  }
                }),
                _vm._v(" "),
                _c("polygon", {
                  staticClass: "stroke",
                  attrs: {
                    fill: "#FFF",
                    points: " 500.1 117 462.2 133 462.2 47 500.1 31 "
                  }
                }),
                _vm._v(" "),
                _c("polygon", {
                  attrs: {
                    opacity: "0.4",
                    fill: "#0170BB",
                    points:
                      "394.1 47 394.5 81.5 408.5 70.5 422.5 81.5 422.5 47 463.3 31 431.7 31 "
                  }
                }),
                _vm._v(" "),
                _c("polygon", {
                  staticClass: "stroke",
                  attrs: {
                    fill: "none",
                    points:
                      " 394.1 47 394.5 81.5 408.5 70.5 422.5 81.5 422.5 47 463.3 31 431.7 31 "
                  }
                })
              ]),
              _vm._v(" "),
              _c("path", {
                staticClass: "stroke",
                attrs: {
                  id: "tshirt",
                  fill: "#FFF",
                  d:
                    "M442 717h35.7c1.7 0 3-1.5 3-3.4v-59.2c0-2.6 2.2-4.4 4.3-3.6l10.4 3.8c3.8 2.2 4.5 0.7 7.1-4.7l7.3-14.5c1.6-2.8 0.7-4.6-1.9-6.9C486 611.1 464.7 608 464.7 608c-1.5 0-2.7 1.2-3 2.9 -0.7 4.8-6.7 14.6-17.4 14.6s-16.7-9.8-17.4-14.6c-0.2-1.7-1.5-2.9-3-2.9 0 0-21.3 3-43.2 20.5 -2.6 2.4-3.5 4.1-1.9 6.9l7.3 14.5c2.7 5.4 3.3 6.8 7.1 4.7l10.4-3.8c2.1-0.8 4.3 1 4.3 3.6v59.2c0 1.9 1.3 3.4 3 3.4h35.7H442z"
                }
              }),
              _vm._v(" "),
              _c("g", { attrs: { id: "cap" } }, [
                _c("path", {
                  staticClass: "stroke",
                  attrs: {
                    fill: "#FFF",
                    d:
                      "M495.9 829.4c-0.4 33-19.4 8.5-50 8.5 -31.4 0-50.4 24.5-50-8.5 0.3-27.9 0.6-62.5 50-62.5C495.5 766.9 496.2 801.5 495.9 829.4z"
                  }
                }),
                _vm._v(" "),
                _c("path", {
                  staticClass: "stroke",
                  attrs: {
                    fill: "none",
                    d: "M396.4 824.4c0 0 18.9-8 49.5-8s49.5 8 49.5 8"
                  }
                }),
                _vm._v(" "),
                _c("ellipse", {
                  attrs: {
                    fill: "#0170BB",
                    cx: "445.9",
                    cy: "763.4",
                    rx: "8.5",
                    ry: "3"
                  }
                }),
                _vm._v(" "),
                _c("path", {
                  staticClass: "stroke",
                  attrs: {
                    fill: "none",
                    d:
                      "M406.4 819.4c0-20.7-4.8-52 39.5-52.5 44.7-0.5 39.5 31.8 39.5 52.5"
                  }
                }),
                _vm._v(" "),
                _c("line", {
                  staticClass: "stroke",
                  attrs: {
                    fill: "none",
                    x1: "445.9",
                    y1: "766.4",
                    x2: "445.9",
                    y2: "816.4"
                  }
                }),
                _vm._v(" "),
                _c("circle", {
                  attrs: { fill: "#0170BB", cx: "429.4", cy: "777.4", r: "2" }
                }),
                _vm._v(" "),
                _c("circle", {
                  attrs: { fill: "#0170BB", cx: "462.4", cy: "777.4", r: "2" }
                })
              ]),
              _vm._v(" "),
              _c("g", { attrs: { id: "ball" } }, [
                _c("circle", {
                  staticClass: "stroke",
                  attrs: { fill: "#FFF", cx: "446", cy: "803.8", r: "47.3" }
                }),
                _vm._v(" "),
                _c("line", {
                  staticClass: "stroke",
                  attrs: {
                    fill: "none",
                    x1: "446",
                    y1: "756.8",
                    x2: "446",
                    y2: "850.8"
                  }
                }),
                _vm._v(" "),
                _c("line", {
                  staticClass: "stroke",
                  attrs: {
                    fill: "none",
                    x1: "493",
                    y1: "804.3",
                    x2: "399",
                    y2: "804.3"
                  }
                }),
                _vm._v(" "),
                _c("path", {
                  staticClass: "stroke",
                  attrs: {
                    fill: "none",
                    d:
                      "M484.2 834c-9.1-6.3-22.8-16.4-38.2-16.4s-29.1 10-38.2 16.4"
                  }
                }),
                _vm._v(" "),
                _c("path", {
                  staticClass: "stroke",
                  attrs: {
                    fill: "none",
                    d:
                      "M407.8 774.6c9.1 6.3 22.8 16.4 38.2 16.4s29.1-10 38.2-16.4"
                  }
                })
              ]),
              _vm._v(" "),
              _c("g", { attrs: { id: "grass" } }, [
                _c("path", {
                  attrs: {
                    fill: "#a5c7e4",
                    d:
                      "M1226.5 857.5c4.7-20.9-7-33.3-20.4-41.3 -2-1.2-4-2.2-6.1-3.1 4.6 8.1 4.6 18.2-1 26.5 -1.3 1.9-2.7 3.5-4.4 5 -1.9-5.6-4.8-11.1-8.9-16 -5.7-6.9-12.9-12.1-20.9-15.4 6.6 10.9 4 24.9-6.5 33 -10.1-7.4-13.4-20.4-8.2-31.3 -7.6 4-14.3 9.8-19.3 17.2 -3.2 4.8-5.5 9.8-6.9 15 -2-1.4-3.8-3.1-5.4-5 -6.4-7.8-7.4-17.8-3.6-26.3 -2 1.1-3.6 2.8-5.7 3.6 -7.2 2.9-9.8 11.8-10.5 21 -3.7-12.9-11.1-24.1-11.1-24.1 -2-1.2-4-2.2-6.1-3.1 4.6 8.1 4.6 18.2-1 26.5 -1.3 1.9-2.7 3.5-4.4 5 -1.9-5.6-4.8-11.1-8.9-16 -5.7-6.9-12.9-12.1-20.9-15.4 6.6 10.9 4 24.9-6.5 33 -10.1-7.4-13.4-20.4-8.2-31.3 -7.6 4-14.3 9.8-19.3 17.2 -3.2 4.8-5.5 9.8-6.9 15 -2-1.4-3.8-3.1-5.4-5 -6.4-7.8-7.4-17.8-3.6-26.3 -2 1.1-3.9 2.3-5.7 3.6 -11 8-17.9 19.2-20.2 31.2 -2.6-13.7-11-26.3-24.4-34.3 -2-1.2-4-2.2-6.1-3.1 4.6 8.1 4.6 18.2-1 26.5 -1.3 1.9-2.7 3.5-4.4 5 -1.9-5.6-4.8-11.1-8.9-16 -5.7-6.9-12.9-12.1-20.9-15.4 6.6 10.9 4 24.9-6.5 33 -10.1-7.4-13.4-20.4-8.2-31.3 -7.6 4-14.3 9.8-19.3 17.2 -3.2 4.8-5.5 9.8-6.8 15 -2-1.4-3.8-3.1-5.4-5 -6.4-7.8-7.4-17.8-3.6-26.3 -2 1.1-3.9 2.3-5.7 3.6 -8.1 5.9-14 13.6-17.5 22 -4-10-11.4-19-21.7-25.2 -2-1.2-4-2.2-6.1-3.1 4.6 8.1 4.6 18.2-1 26.5 -1.3 1.9-2.7 3.5-4.4 5 -1.9-5.6-4.8-11.1-8.9-16 -5.7-6.9-12.9-12.1-20.9-15.4 6.6 10.9 4 24.9-6.5 33 -10.1-7.4-13.4-20.4-8.2-31.3 -7.6 4-14.3 9.8-19.3 17.2 -3.2 4.8-5.5 9.8-6.9 15 -2-1.4-3.8-3.1-5.4-5 -6.4-7.8-7.4-17.8-3.6-26.3 -2 1.1-3.9 2.3-5.7 3.6 -11 8-17.9 19.2-20.2 31.2 -2.6-13.7-11-26.3-24.4-34.3 -2-1.2-4-2.2-6.1-3.1 4.6 8.1 4.6 18.2-1 26.5 -1.3 1.9-2.7 3.5-4.4 5 -1.9-5.6-4.8-11.1-8.9-16 -5.7-6.9-12.9-12.1-20.9-15.4 6.6 10.9 4 24.9-6.5 33 -10.1-7.4-13.4-20.4-8.2-31.3 -7.6 4-14.3 9.8-19.3 17.2 -3.2 4.8-5.5 9.8-6.9 15 -2-1.4-3.8-3.1-5.4-5 -6.4-7.8-7.4-17.8-3.6-26.3 -2 1.1-3.9 2.3-5.7 3.6 -8.5 6.2-14.5 14.2-17.9 23 -3.9-10.4-11.4-19.8-22.1-26.2 -2-1.2-4-2.2-6.1-3.1 4.6 8.1 4.6 18.2-1 26.5 -1.3 1.9-2.7 3.5-4.4 5 -1.9-5.6-4.8-11.1-8.9-16 -5.7-6.9-12.9-12.1-20.9-15.4 6.6 10.9 4 24.9-6.5 33 -10.1-7.4-13.4-20.4-8.2-31.3 -7.6 4-14.3 9.8-19.3 17.2 -3.2 4.8-5.5 9.8-6.8 15 -2-1.4-3.8-3.1-5.4-5 -6.4-7.8-7.4-17.8-3.6-26.3 -2 1.1-3.9 2.3-5.7 3.6 -11 8-17.9 19.2-20.2 31.2 -2.6-13.7-11-26.3-24.4-34.3 -2-1.2-4-2.2-6.1-3.1 4.6 8.1 4.6 18.2-1 26.5 -1.3 1.9-2.7 3.5-4.4 5 -1.9-5.6-4.8-11.1-8.9-16 -5.7-6.9-12.9-12.1-20.9-15.4 6.6 10.9 4 24.9-6.5 33 -10.1-7.4-13.4-20.4-8.2-31.3 -7.6 4-14.3 9.8-19.3 17.2 -3.2 4.8-5.5 9.8-6.9 15 -2-1.4-3.8-3.1-5.4-5 -6.4-7.8-7.4-17.8-3.6-26.3 -2 1.1-3.9 2.3-5.7 3.6 -8.1 5.9-14 13.6-17.5 22 -4-10-11.4-19-21.7-25.2 -2-1.2-4-2.2-6.1-3.1 4.6 8.1 4.6 18.2-1 26.5 -1.3 1.9-2.7 3.5-4.4 5 -1.9-5.6-4.8-11.1-8.9-16 -5.7-6.9-12.9-12.1-20.9-15.4 6.6 10.9 4 24.9-6.5 33 -10.1-7.4-13.4-20.4-8.2-31.3 -7.6 4-14.3 9.8-19.3 17.2 -3.2 4.8-5.5 9.8-6.9 15 -2-1.4-3.8-3.1-5.4-5 -6.4-7.8-7.4-17.8-3.6-26.3 -2 1.1-3.9 2.3-5.7 3.6 -11 8-17.9 19.2-20.2 31.2 -2.6-13.7-11-26.3-24.4-34.3 -2-1.2-4-2.2-6.1-3.1 4.6 8.1 4.6 18.2-1 26.5 -1.3 1.9-2.7 3.5-4.4 5 -1.9-5.6-4.8-11.1-8.9-16 -5.7-6.9-12.9-12.1-20.9-15.4 6.6 10.9 4 24.9-6.5 33 -10.1-7.4-13.4-20.4-8.2-31.3 -7.6 4-14.3 9.8-19.3 17.2 -3.2 4.8-5.5 9.8-6.9 15 -2-1.4-3.8-3.1-5.4-5 -6.4-7.8-7.4-17.8-3.6-26.3 -2 1.1-3.9 2.3-5.7 3.6 -27.2 20.2-8.8 45.6-8.8 45.6"
                  }
                }),
                _vm._v(" "),
                _c("path", {
                  staticClass: "stroke round-end",
                  attrs: {
                    fill: "none",
                    d:
                      "M1226.5 857.5c4.7-20.9-7-33.3-20.4-41.3 -2-1.2-4-2.2-6.1-3.1 4.6 8.1 4.6 18.2-1 26.5 -1.3 1.9-2.7 3.5-4.4 5 -1.9-5.6-4.8-11.1-8.9-16 -5.7-6.9-12.9-12.1-20.9-15.4 6.6 10.9 4 24.9-6.5 33 -10.1-7.4-13.4-20.4-8.2-31.3 -7.6 4-14.3 9.8-19.3 17.2 -3.2 4.8-5.5 9.8-6.9 15 -2-1.4-3.8-3.1-5.4-5 -6.4-7.8-7.4-17.8-3.6-26.3 -2 1.1-3.6 2.8-5.7 3.6 -7.2 2.9-9.8 11.8-10.5 21 -3.7-12.9-11.1-24.1-11.1-24.1 -2-1.2-4-2.2-6.1-3.1 4.6 8.1 4.6 18.2-1 26.5 -1.3 1.9-2.7 3.5-4.4 5 -1.9-5.6-4.8-11.1-8.9-16 -5.7-6.9-12.9-12.1-20.9-15.4 6.6 10.9 4 24.9-6.5 33 -10.1-7.4-13.4-20.4-8.2-31.3 -7.6 4-14.3 9.8-19.3 17.2 -3.2 4.8-5.5 9.8-6.9 15 -2-1.4-3.8-3.1-5.4-5 -6.4-7.8-7.4-17.8-3.6-26.3 -2 1.1-3.9 2.3-5.7 3.6 -11 8-17.9 19.2-20.2 31.2 -2.6-13.7-11-26.3-24.4-34.3 -2-1.2-4-2.2-6.1-3.1 4.6 8.1 4.6 18.2-1 26.5 -1.3 1.9-2.7 3.5-4.4 5 -1.9-5.6-4.8-11.1-8.9-16 -5.7-6.9-12.9-12.1-20.9-15.4 6.6 10.9 4 24.9-6.5 33 -10.1-7.4-13.4-20.4-8.2-31.3 -7.6 4-14.3 9.8-19.3 17.2 -3.2 4.8-5.5 9.8-6.8 15 -2-1.4-3.8-3.1-5.4-5 -6.4-7.8-7.4-17.8-3.6-26.3 -2 1.1-3.9 2.3-5.7 3.6 -8.1 5.9-14 13.6-17.5 22 -4-10-11.4-19-21.7-25.2 -2-1.2-4-2.2-6.1-3.1 4.6 8.1 4.6 18.2-1 26.5 -1.3 1.9-2.7 3.5-4.4 5 -1.9-5.6-4.8-11.1-8.9-16 -5.7-6.9-12.9-12.1-20.9-15.4 6.6 10.9 4 24.9-6.5 33 -10.1-7.4-13.4-20.4-8.2-31.3 -7.6 4-14.3 9.8-19.3 17.2 -3.2 4.8-5.5 9.8-6.9 15 -2-1.4-3.8-3.1-5.4-5 -6.4-7.8-7.4-17.8-3.6-26.3 -2 1.1-3.9 2.3-5.7 3.6 -11 8-17.9 19.2-20.2 31.2 -2.6-13.7-11-26.3-24.4-34.3 -2-1.2-4-2.2-6.1-3.1 4.6 8.1 4.6 18.2-1 26.5 -1.3 1.9-2.7 3.5-4.4 5 -1.9-5.6-4.8-11.1-8.9-16 -5.7-6.9-12.9-12.1-20.9-15.4 6.6 10.9 4 24.9-6.5 33 -10.1-7.4-13.4-20.4-8.2-31.3 -7.6 4-14.3 9.8-19.3 17.2 -3.2 4.8-5.5 9.8-6.9 15 -2-1.4-3.8-3.1-5.4-5 -6.4-7.8-7.4-17.8-3.6-26.3 -2 1.1-3.9 2.3-5.7 3.6 -8.5 6.2-14.5 14.2-17.9 23 -3.9-10.4-11.4-19.8-22.1-26.2 -2-1.2-4-2.2-6.1-3.1 4.6 8.1 4.6 18.2-1 26.5 -1.3 1.9-2.7 3.5-4.4 5 -1.9-5.6-4.8-11.1-8.9-16 -5.7-6.9-12.9-12.1-20.9-15.4 6.6 10.9 4 24.9-6.5 33 -10.1-7.4-13.4-20.4-8.2-31.3 -7.6 4-14.3 9.8-19.3 17.2 -3.2 4.8-5.5 9.8-6.8 15 -2-1.4-3.8-3.1-5.4-5 -6.4-7.8-7.4-17.8-3.6-26.3 -2 1.1-3.9 2.3-5.7 3.6 -11 8-17.9 19.2-20.2 31.2 -2.6-13.7-11-26.3-24.4-34.3 -2-1.2-4-2.2-6.1-3.1 4.6 8.1 4.6 18.2-1 26.5 -1.3 1.9-2.7 3.5-4.4 5 -1.9-5.6-4.8-11.1-8.9-16 -5.7-6.9-12.9-12.1-20.9-15.4 6.6 10.9 4 24.9-6.5 33 -10.1-7.4-13.4-20.4-8.2-31.3 -7.6 4-14.3 9.8-19.3 17.2 -3.2 4.8-5.5 9.8-6.9 15 -2-1.4-3.8-3.1-5.4-5 -6.4-7.8-7.4-17.8-3.6-26.3 -2 1.1-3.9 2.3-5.7 3.6 -8.1 5.9-14 13.6-17.5 22 -4-10-11.4-19-21.7-25.2 -2-1.2-4-2.2-6.1-3.1 4.6 8.1 4.6 18.2-1 26.5 -1.3 1.9-2.7 3.5-4.4 5 -1.9-5.6-4.8-11.1-8.9-16 -5.7-6.9-12.9-12.1-20.9-15.4 6.6 10.9 4 24.9-6.5 33 -10.1-7.4-13.4-20.4-8.2-31.3 -7.6 4-14.3 9.8-19.3 17.2 -3.2 4.8-5.5 9.8-6.9 15 -2-1.4-3.8-3.1-5.4-5 -6.4-7.8-7.4-17.8-3.6-26.3 -2 1.1-3.9 2.3-5.7 3.6 -11 8-17.9 19.2-20.2 31.2 -2.6-13.7-11-26.3-24.4-34.3 -2-1.2-4-2.2-6.1-3.1 4.6 8.1 4.6 18.2-1 26.5 -1.3 1.9-2.7 3.5-4.4 5 -1.9-5.6-4.8-11.1-8.9-16 -5.7-6.9-12.9-12.1-20.9-15.4 6.6 10.9 4 24.9-6.5 33 -10.1-7.4-13.4-20.4-8.2-31.3 -7.6 4-14.3 9.8-19.3 17.2 -3.2 4.8-5.5 9.8-6.9 15 -2-1.4-3.8-3.1-5.4-5 -6.4-7.8-7.4-17.8-3.6-26.3 -2 1.1-3.9 2.3-5.7 3.6 -27.2 20.2-8.8 45.6-8.8 45.6"
                  }
                })
              ]),
              _vm._v(" "),
              _c("g", { attrs: { id: "plane" } }, [
                _c("path", {
                  staticClass: "stroke",
                  attrs: {
                    fill: "#FFF",
                    d:
                      "M966.1 203.5c0 0 70.8 0.9 70.8 10.7 0 20.6-23.3 41.3-88.7 43 -34 0.9-98.5 3.6-120-1.8 -30.5-7.6-109.1-44-112-52.8 -13.4-41.2-18.8-49.3 2.7-49.3 12 0 18.6 0 26 0 14.3 0 12.5 2.7 27.8 42.1 0 0 50.2 8.1 66.3-1.8s24.6-23.3 57.6-23.4l21 0.1C938.5 171.3 949.5 176.3 966.1 203.5z"
                  }
                }),
                _vm._v(" "),
                _c("path", {
                  attrs: {
                    fill: "#a5c7e4",
                    d:
                      "M896.5 182.4v18c0 1.1-0.9 2-2 2h-39.6c-1.8 0-2.7-2.1-1.5-3.4 5.7-6 19.6-17.9 41-18.6C895.5 180.3 896.5 181.2 896.5 182.4z"
                  }
                }),
                _vm._v(" "),
                _c("path", {
                  attrs: {
                    fill: "#a5c7e4",
                    d:
                      "M906.5 182.4v18c0 1.1 0.9 2 2 2h39.6c1.8 0 2.4-1.9 1.5-3.4 -6.1-9.6-12.1-18.6-41-18.6C907.4 180.4 906.5 181.2 906.5 182.4z"
                  }
                }),
                _vm._v(" "),
                _c("path", {
                  staticClass: "stroke",
                  attrs: {
                    fill: "none",
                    d:
                      "M896.5 182.4v18c0 1.1-0.9 2-2 2h-39.6c-1.8 0-2.7-2.1-1.5-3.4 5.7-6 19.6-17.9 41-18.6C895.5 180.3 896.5 181.2 896.5 182.4z"
                  }
                }),
                _vm._v(" "),
                _c("path", {
                  staticClass: "stroke",
                  attrs: {
                    fill: "none",
                    d:
                      "M906.5 182.4v18c0 1.1 0.9 2 2 2h39.6c1.8 0 2.4-1.9 1.5-3.4 -6.1-9.6-12.1-18.6-41-18.6C907.4 180.4 906.5 181.2 906.5 182.4z"
                  }
                }),
                _vm._v(" "),
                _c("path", {
                  attrs: {
                    fill: "#a5c7e4",
                    d:
                      "M745.3 193.7h-58.2c-3.7 0-6.7-3-6.7-6.7v0c0-3.7 3-6.7 6.7-6.7h58.2c3.7 0 6.7 3 6.7 6.7v0C752 190.6 749 193.7 745.3 193.7z"
                  }
                }),
                _vm._v(" "),
                _c("g", { attrs: { id: "helix" } }, [
                  _c("path", {
                    attrs: {
                      fill: "#0170BB",
                      d:
                        "M1037.8 233.5h-1.8c-4.2 0-3.1-12.1-3.1-12.1s-1.1-12.1 3.1-12.1l0 0c5.2 0 9.4 4.2 9.4 9.4v7.2C1045.4 230.1 1041.9 233.5 1037.8 233.5z"
                    }
                  }),
                  _vm._v(" "),
                  _c("path", {
                    attrs: {
                      fill: "#a5c7e4",
                      d:
                        "M1037.2 214.4L1037.2 214.4c-4.6 0-8.3-34-8.3-34 0-4.6 3.8-8.3 8.3-8.3h0c4.6 0 8.3 3.8 8.3 8.3C1045.6 180.3 1041.8 214.4 1037.2 214.4z"
                    }
                  }),
                  _vm._v(" "),
                  _c("path", {
                    attrs: {
                      fill: "#a5c7e4",
                      d:
                        "M1037.2 228.5L1037.2 228.5c4.6 0 8.3 34 8.3 34 0 4.6-3.8 8.3-8.3 8.3h0c-4.6 0-8.3-3.8-8.3-8.3C1028.9 262.5 1032.7 228.5 1037.2 228.5z"
                    }
                  }),
                  _vm._v(" "),
                  _c("path", {
                    staticClass: "stroke",
                    attrs: {
                      fill: "none",
                      d:
                        "M1037.2 214.4L1037.2 214.4c-4.6 0-8.3-34-8.3-34 0-4.6 3.8-8.3 8.3-8.3h0c4.6 0 8.3 3.8 8.3 8.3C1045.6 180.3 1041.8 214.4 1037.2 214.4z"
                    }
                  }),
                  _vm._v(" "),
                  _c("path", {
                    staticClass: "stroke",
                    attrs: {
                      fill: "none",
                      d:
                        "M1037.2 228.5L1037.2 228.5c4.6 0 8.3 34 8.3 34 0 4.6-3.8 8.3-8.3 8.3h0c-4.6 0-8.3-3.8-8.3-8.3C1028.9 262.5 1032.7 228.5 1037.2 228.5z"
                    }
                  })
                ]),
                _vm._v(" "),
                _c("use", {
                  staticClass: "helix",
                  attrs: { "xlink:href": "#helix", filter: "url(#f1)" }
                }),
                _vm._v(" "),
                _c("line", {
                  staticClass: "stroke",
                  attrs: {
                    fill: "none",
                    x1: "728",
                    y1: "213.3",
                    x2: "520",
                    y2: "213.2"
                  }
                }),
                _vm._v(" "),
                _c("polyline", {
                  staticClass: "stroke",
                  attrs: {
                    fill: "none",
                    points: "520 182.8 558.5 214.2 520 243.7 "
                  }
                }),
                _vm._v(" "),
                _c("path", {
                  staticClass: "stroke",
                  attrs: {
                    fill: "#FFF",
                    d:
                      "M506.9 253.6H21.2c-6.6 0-12-5.4-12-12v-56.7c0-6.6 5.4-12 12-12h485.8c6.6 0 12 5.4 12 12v56.7C518.9 248.2 513.5 253.6 506.9 253.6z"
                  }
                }),
                _vm._v(" "),
                _c(
                  "text",
                  {
                    attrs: {
                      transform: "matrix(1.0027 0 0 1 44.8218 224.8768)",
                      "font-family": "Fredoka One",
                      "font-size": "34",
                      fill: "#0170BB"
                    }
                  },
                  [_vm._v(" still under development... ")]
                ),
                _vm._v(" "),
                _c("path", {
                  attrs: {
                    fill: "#a5c7e4",
                    d:
                      "M850.5 216.5h79.7l-4.5 10.7c0 0-2.7 7.2-9.9 7.2h-72.6c0 0-6.3-0.9-1.8-7.2L850.5 216.5z"
                  }
                }),
                _vm._v(" "),
                _c("path", {
                  staticClass: "stroke",
                  attrs: {
                    fill: "none",
                    d:
                      "M745.3 193.7h-58.2c-3.7 0-6.7-3-6.7-6.7v0c0-3.7 3-6.7 6.7-6.7h58.2c3.7 0 6.7 3 6.7 6.7v0C752 190.6 749 193.7 745.3 193.7z"
                  }
                }),
                _vm._v(" "),
                _c("path", {
                  staticClass: "stroke",
                  attrs: {
                    fill: "none",
                    d:
                      "M850.5 216.5h79.7l-4.5 10.7c0 0-2.7 7.2-9.9 7.2h-72.6c0 0-6.3-0.9-1.8-7.2L850.5 216.5z"
                  }
                })
              ])
            ]),
            _vm._v(" "),
            _c("g", { attrs: { id: "window" } }, [
              _c("path", {
                attrs: {
                  opacity: "0.4",
                  fill: "#0170BB",
                  d:
                    "M683.6 773H368c-8.1 0-14.7-6.6-14.7-14.7V565.2c0-8.1 6.6-14.7 14.7-14.7h315.6c8.1 0 14.7 6.6 14.7 14.7v193.1C698.3 766.4 691.7 773 683.6 773z"
                }
              }),
              _vm._v(" "),
              _c("path", {
                staticClass: "stroke",
                attrs: {
                  fill: "none",
                  d:
                    "M683.6 773H368c-8.1 0-14.7-6.6-14.7-14.7V565.2c0-8.1 6.6-14.7 14.7-14.7h315.6c8.1 0 14.7 6.6 14.7 14.7v193.1C698.3 766.4 691.7 773 683.6 773z"
                }
              })
            ]),
            _vm._v(" "),
            _c("use", {
              staticClass: "box",
              attrs: { "xlink:href": "#box", x: "20", y: "30" }
            }),
            _vm._v(" "),
            _c("use", {
              staticClass: "parachute",
              attrs: { "xlink:href": "#parachute", x: "20", y: "-112" }
            }),
            _vm._v(" "),
            _c("rect", {
              attrs: {
                fill: "white",
                x: "320",
                y: "310",
                width: "665",
                height: "238"
              }
            }),
            _vm._v(" "),
            _c("use", {
              staticClass: "tshirt",
              attrs: { "xlink:href": "#tshirt" }
            }),
            _vm._v(" "),
            _c("use", {
              staticClass: "cap",
              attrs: { "xlink:href": "#cap", y: "-150" }
            }),
            _vm._v(" "),
            _c("use", {
              staticClass: "ball",
              attrs: { "xlink:href": "#ball", y: "-140" }
            }),
            _vm._v(" "),
            _c("use", {
              staticClass: "sky-circle",
              attrs: { "xlink:href": "#sky-circle", x: "-10px", y: "5" }
            }),
            _vm._v(" "),
            _c("use", {
              staticClass: "sky-circle2",
              attrs: { "xlink:href": "#sky-circle", x: "500px", y: "-125" }
            }),
            _vm._v(" "),
            _c("use", {
              staticClass: "sky-circle3",
              attrs: { "xlink:href": "#sky-circle", x: "1000px", y: "50" }
            }),
            _vm._v(" "),
            _c("use", {
              staticClass: "cloud",
              attrs: { "xlink:href": "#cloud2", x: "0", y: "10" }
            }),
            _vm._v(" "),
            _c("use", {
              staticClass: "plane",
              attrs: { "xlink:href": "#plane", y: "-80" }
            }),
            _vm._v(" "),
            _c("use", {
              staticClass: "cloud2",
              attrs: { "xlink:href": "#cloud", x: "0", y: "130" }
            }),
            _vm._v(" "),
            _c("use", {
              staticClass: "trees",
              attrs: { "xlink:href": "#tree", x: "40", y: "0" }
            }),
            _vm._v(" "),
            _c("circle", {
              staticClass: "cat-shadow",
              attrs: { fill: "#0170BB", cx: "1160", cy: "620", r: "23" }
            }),
            _vm._v(" "),
            _c("use", {
              staticClass: "cat",
              attrs: { "xlink:href": "#cat", x: "15", y: "5" }
            }),
            _vm._v(" "),
            _c("g", { attrs: { id: "browser" } }, [
              _c("path", {
                staticClass: "stroke",
                attrs: {
                  fill: "none",
                  d:
                    "M972.4 847h-640c-8.2 0-15-6.8-15-15V322.5c0-8.2 6.8-15 15-15h640c8.2 0 15 6.8 15 15V832C987.4 840.3 980.7 847 972.4 847z"
                }
              }),
              _vm._v(" "),
              _c("circle", {
                attrs: {
                  opacity: "0.4",
                  fill: "#ED4F43",
                  cx: "363.7",
                  cy: "349.3",
                  r: "12.3"
                }
              }),
              _vm._v(" "),
              _c("circle", {
                staticClass: "stroke",
                attrs: { fill: "none", cx: "402.2", cy: "349.3", r: "12.3" }
              }),
              _vm._v(" "),
              _c("path", {
                staticClass: "stroke",
                attrs: {
                  fill: "none",
                  stroke: "#0170BB",
                  d:
                    "M943.5 361.5H454.1c-5.5 0-9.9-4.5-9.9-9.9V344c0-5.5 4.5-9.9 9.9-9.9h489.4c5.5 0 9.9 4.5 9.9 9.9v7.6C953.4 357.1 949 361.5 943.5 361.5z"
                }
              }),
              _vm._v(" "),
              _c("circle", {
                staticClass: "stroke",
                attrs: { fill: "none", cx: "363.7", cy: "349.3", r: "12.3" }
              })
            ]),
            _vm._v(" "),
            _c("g", { attrs: { id: "toldo" } }, [
              _c("polyline", {
                staticClass: "stroke round-end",
                attrs: {
                  fill: "#FFF",
                  points: " 277.6 468.6 317.7 391.8 987.6 391.8 1026.9 468.6 "
                }
              }),
              _vm._v(" "),
              _c("path", {
                staticClass: "stroke",
                attrs: {
                  fill: "#FFF",
                  d:
                    "M392.2 391.8l-31.3 79.5c0 22.7 18.4 41 41 41 22.7 0 41-18.4 41-41"
                }
              }),
              _vm._v(" "),
              _c("path", {
                staticClass: "stroke",
                attrs: {
                  fill: "#FFF",
                  d:
                    "M466.6 391.8l-22.3 79.5c0 22.7 18.4 41 41 41s41-18.4 41-41"
                }
              }),
              _vm._v(" "),
              _c("path", {
                staticClass: "stroke",
                attrs: {
                  id: "tol",
                  fill: "#FFF",
                  d: "M527.6 471.2c0 22.7 18.4 41 41 41 22.7 0 41-18.4 41-41"
                }
              }),
              _vm._v(" "),
              _c("path", {
                staticClass: "stroke",
                attrs: {
                  fill: "#FFF",
                  d:
                    "M615.5 391.8l-4.5 79.5c0 22.7 18.4 41 41 41 22.7 0 41-18.4 41-41"
                }
              }),
              _vm._v(" "),
              _c("path", {
                staticClass: "stroke",
                attrs: {
                  fill: "#FFF",
                  d: "M689.9 391.8l4.4 79.5c0 22.7 18.4 41 41 41s41-18.4 41-41"
                }
              }),
              _vm._v(" "),
              _c("path", {
                staticClass: "stroke",
                attrs: {
                  fill: "#FFF",
                  d:
                    "M859.7 471.2c0 22.7-18.4 41-41 41 -22.7 0-41-18.4-41-41l-13.3-79.5"
                }
              }),
              _vm._v(" "),
              _c("use", {
                staticClass: "tol",
                attrs: { "xlink:href": "#tol", x: "-250" }
              }),
              _vm._v(" "),
              _c("use", {
                staticClass: "tol",
                attrs: { "xlink:href": "#tol", x: "334" }
              }),
              _vm._v(" "),
              _c("use", {
                staticClass: "tol",
                attrs: { "xlink:href": "#tol", x: "417" }
              }),
              _vm._v(" "),
              _c("line", {
                staticClass: "stroke round-end",
                attrs: { x1: "277", y1: "470.5", x2: "1027", y2: "470.5" }
              }),
              _vm._v(" "),
              _c("line", {
                staticClass: "stroke",
                attrs: { x1: "541", y1: "391.8", x2: "526.5", y2: "471.2" }
              }),
              _vm._v(" "),
              _c("line", {
                staticClass: "stroke",
                attrs: { x1: "838.8", y1: "391.8", x2: "860.1", y2: "471.2" }
              }),
              _vm._v(" "),
              _c("path", {
                attrs: {
                  opacity: "0.4",
                  fill: "#0170BB",
                  d:
                    "M467.3 392.1h73.4l-14 79.5c0 22.7-18.4 41-41 41 -22.7 0-41-18.4-41-41L467.3 392.1z"
                }
              }),
              _vm._v(" "),
              _c("path", {
                attrs: {
                  opacity: "0.4",
                  fill: "#0170BB",
                  d:
                    "M615.7 392.1H690l3.5 79.5c0 22.7-18.4 41-41 41 -22.7 0-41-18.4-41-41L615.7 392.1z"
                }
              }),
              _vm._v(" "),
              _c("path", {
                attrs: {
                  opacity: "0.4",
                  fill: "#0170BB",
                  d:
                    "M765.1 392.1h73.4l21.8 79.5c0 22.7-18.4 41-41 41s-41-18.4-41-41L765.1 392.1z"
                }
              }),
              _vm._v(" "),
              _c("path", {
                attrs: {
                  opacity: "0.4",
                  fill: "#0170BB",
                  d:
                    "M913.6 392.1h73.4l40.2 79.5c0 22.7-18.4 41-41 41 -22.7 0-41-18.4-41-41L913.6 392.1z"
                }
              }),
              _vm._v(" "),
              _c("path", {
                attrs: {
                  opacity: "0.4",
                  fill: "#0170BB",
                  d:
                    "M317.9 392.1h73.4l-31.4 79.5c0 22.7-18.4 41-41 41 -22.7 0-41-18.4-41-41L317.9 392.1z"
                }
              }),
              _vm._v(" "),
              _c("line", {
                staticClass: "stroke",
                attrs: {
                  fill: "none",
                  x1: "944.4",
                  y1: "471.6",
                  x2: "913.2",
                  y2: "392.2"
                }
              })
            ]),
            _vm._v(" "),
            _c("g", { attrs: { id: "door" } }, [
              _c("path", {
                staticClass: "stroke",
                attrs: {
                  fill: "none",
                  d:
                    "M955.8 846V560.5c0-5.5-4.5-10-10-10H738.6c-5.5 0-10 4.5-10 10V846"
                }
              }),
              _vm._v(" "),
              _c("rect", {
                attrs: {
                  fill: "#0170BB",
                  x: "730",
                  y: "700",
                  width: "225",
                  height: "15"
                }
              }),
              _vm._v(" "),
              _c("g", { attrs: { id: "sign" } }, [
                _c("polyline", {
                  staticClass: "stroke",
                  attrs: {
                    fill: "none",
                    points: " 800.8 672.8 842.5 601 883.6 672.8 "
                  }
                }),
                _vm._v(" "),
                _c("ellipse", {
                  staticClass: "stroke",
                  attrs: {
                    fill: "#FFF",
                    cx: "842.2",
                    cy: "601",
                    rx: "10",
                    ry: "10"
                  }
                }),
                _vm._v(" "),
                _c("path", {
                  attrs: {
                    fill: "#a5c7e4",
                    d:
                      "M909.3 740.7H775.1c-5.5 0-10-4.5-10-10v-47.9c0-5.5 4.5-10 10-10h134.2c5.5 0 10 4.5 10 10v47.9C919.3 736.2 914.8 740.7 909.3 740.7z"
                  }
                }),
                _vm._v(" "),
                _c(
                  "text",
                  {
                    attrs: {
                      transform: "matrix(1.0027 0 0 1 789.6294 721.7501)",
                      fill: "#FFF",
                      "font-family": "Fredoka One",
                      "font-size": "38"
                    }
                  },
                  [_vm._v(" Closed ")]
                ),
                _vm._v(" "),
                _c("path", {
                  staticClass: "stroke",
                  attrs: {
                    fill: "none",
                    d:
                      "M909.3 740.7H775.1c-5.5 0-10-4.5-10-10v-47.9c0-5.5 4.5-10 10-10h134.2c5.5 0 10 4.5 10 10v47.9C919.3 736.2 914.8 740.7 909.3 740.7z"
                  }
                })
              ])
            ]),
            _vm._v(" "),
            _c("g", { attrs: { id: "button" } }, [
              _c("path", {
                attrs: {
                  opacity: "0.4",
                  fill: "#0170BB",
                  d:
                    "M650.5 725.5H547.8c-4.7 0-8.6-3.9-8.6-8.6v-18.1c0-4.7 3.9-8.6 8.6-8.6h102.7c4.7 0 8.6 3.9 8.6 8.6v18.1C659.2 721.7 655.3 725.5 650.5 725.5z"
                }
              }),
              _vm._v(" "),
              _c("path", {
                staticClass: "stroke",
                attrs: {
                  fill: "none",
                  d:
                    "M650.5 725.5H547.8c-4.7 0-8.6-3.9-8.6-8.6v-18.1c0-4.7 3.9-8.6 8.6-8.6h102.7c4.7 0 8.6 3.9 8.6 8.6v18.1C659.2 721.7 655.3 725.5 650.5 725.5z"
                }
              })
            ]),
            _vm._v(" "),
            _c("g", { attrs: { id: "text" } }, [
              _c("line", {
                staticClass: "stroke round-end",
                attrs: {
                  fill: "none",
                  x1: "539.2",
                  y1: "605.5",
                  x2: "652.2",
                  y2: "605.5"
                }
              }),
              _vm._v(" "),
              _c("line", {
                staticClass: "stroke round-end",
                attrs: {
                  fill: "none",
                  x1: "539.2",
                  y1: "630.5",
                  x2: "669.2",
                  y2: "630.5"
                }
              }),
              _vm._v(" "),
              _c("line", {
                staticClass: "stroke round-end",
                attrs: {
                  fill: "none",
                  x1: "539.2",
                  y1: "655.5",
                  x2: "619.2",
                  y2: "655.5"
                }
              })
            ]),
            _vm._v(" "),
            _c("use", {
              staticClass: "grass",
              attrs: { "xlink:href": "#grass", x: "130", y: "0" }
            }),
            _vm._v(" "),
            _c("rect", {
              staticClass: "grass",
              attrs: {
                x: "130",
                y: "850",
                fill: "#a5c7e4",
                width: "100%",
                height: "80"
              }
            })
          ]
        )
      ])
    ])
  ])
}
var staticRenderFns = []
render._withStripped = true



/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/admin/caserecording/components/FileLists.vue?vue&type=template&id=3398ee6c&scoped=true&":
/*!********************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/admin/caserecording/components/FileLists.vue?vue&type=template&id=3398ee6c&scoped=true& ***!
  \********************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "render", function() { return render; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return staticRenderFns; });
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "div",
    { staticClass: "component-wrap" },
    [
      _c(
        "v-card",
        { staticClass: "white--text", attrs: { color: "primary", raised: "" } },
        [
          _c("div", { staticClass: "d-flex flex-column" }, [
            _c(
              "div",
              { staticClass: "flex-grow-1 pa-2" },
              [
                _c("v-text-field", {
                  attrs: {
                    rounded: "",
                    light: "",
                    solo: "",
                    clearable: "",
                    "append-icon": "search",
                    loading: _vm.loading,
                    label: "Search..."
                  },
                  model: {
                    value: _vm.filters.name,
                    callback: function($$v) {
                      _vm.$set(_vm.filters, "name", $$v)
                    },
                    expression: "filters.name"
                  }
                })
              ],
              1
            ),
            _vm._v(" "),
            _c(
              "div",
              { staticClass: "flex-grow-1 px-3" },
              [
                _vm._v("\n                Show Only:\n                "),
                _c(
                  "v-btn-toggle",
                  {
                    attrs: { mandatory: "", rounded: "", light: "" },
                    model: {
                      value: _vm.filter_status,
                      callback: function($$v) {
                        _vm.filter_status = $$v
                      },
                      expression: "filter_status"
                    }
                  },
                  [
                    _c(
                      "v-btn",
                      { attrs: { small: "", value: "", loading: _vm.loading } },
                      [_vm._v("All")]
                    ),
                    _vm._v(" "),
                    _c(
                      "v-btn",
                      {
                        attrs: { small: "", value: "new", loading: _vm.loading }
                      },
                      [_vm._v("New")]
                    ),
                    _vm._v(" "),
                    _c(
                      "v-btn",
                      {
                        attrs: {
                          small: "",
                          value: "onprogress",
                          loading: _vm.loading
                        }
                      },
                      [_vm._v("On Progress")]
                    ),
                    _vm._v(" "),
                    _c(
                      "v-btn",
                      {
                        attrs: {
                          small: "",
                          value: "closed",
                          loading: _vm.loading
                        }
                      },
                      [_vm._v("Closed")]
                    )
                  ],
                  1
                ),
                _vm._v(" "),
                _c(
                  "v-btn-toggle",
                  { staticClass: "float-right", attrs: { light: "" } },
                  [
                    _c(
                      "v-btn",
                      {
                        attrs: { loading: _vm.loading, small: "" },
                        on: {
                          click: function($event) {
                            return _vm.loadFiles()
                          }
                        }
                      },
                      [_c("v-icon", [_vm._v("mdi-refresh")])],
                      1
                    )
                  ],
                  1
                )
              ],
              1
            ),
            _vm._v(" "),
            _c(
              "div",
              { staticClass: "flex-grow-1 pa-2" },
              _vm._l(_vm.filters.fileGroupsHolder, function(group, i) {
                return _c(
                  "span",
                  { key: i },
                  [
                    _c("v-checkbox", {
                      attrs: { label: group.name },
                      model: {
                        value: _vm.filters.fileGroupId[group.id],
                        callback: function($$v) {
                          _vm.$set(_vm.filters.fileGroupId, group.id, $$v)
                        },
                        expression: "filters.fileGroupId[group.id]"
                      }
                    })
                  ],
                  1
                )
              }),
              0
            )
          ])
        ]
      ),
      _vm._v(" "),
      _vm.tableloading == true
        ? _c("v-skeleton-loader", {
            staticClass: "mx-auto",
            attrs: { loading: _vm.tableloading, type: "table", tile: "tile" }
          })
        : _vm._e(),
      _vm._v(" "),
      _c("v-data-table", {
        staticClass: "elevation-1 mytable",
        attrs: {
          headers: _vm.headers,
          options: _vm.pagination,
          search: _vm.filters.name,
          loading: _vm.loading,
          items: _vm.items,
          "server-items-length": _vm.totalItems,
          "footer-props": {
            showFirstLastPage: true,
            "items-per-page-options": [10, 30, 50, 100]
          }
        },
        on: {
          "update:options": function($event) {
            _vm.pagination = $event
          }
        }
      }),
      _vm._v(" "),
      _c(
        "v-dialog",
        {
          attrs: {
            fullscreen: "",
            laze: false,
            transition: "dialog-bottom-transition",
            overlay: false
          },
          model: {
            value: _vm.dialogs.view.show,
            callback: function($$v) {
              _vm.$set(_vm.dialogs.view, "show", $$v)
            },
            expression: "dialogs.view.show"
          }
        },
        [
          _c(
            "v-card",
            [
              _c(
                "v-toolbar",
                { staticClass: "primary" },
                [
                  _c(
                    "v-btn",
                    {
                      attrs: { icon: "", dark: "" },
                      nativeOn: {
                        click: function($event) {
                          _vm.dialogs.view.show = false
                        }
                      }
                    },
                    [_c("v-icon", [_vm._v("close")])],
                    1
                  ),
                  _vm._v(" "),
                  _c("v-toolbar-title", { staticClass: "white--text" }, [
                    _vm._v(_vm._s(_vm.dialogs.view.file.name))
                  ]),
                  _vm._v(" "),
                  _c("v-spacer"),
                  _vm._v(" "),
                  _c(
                    "v-toolbar-items",
                    [
                      _c(
                        "v-btn",
                        {
                          attrs: { dark: "", text: "" },
                          nativeOn: {
                            click: function($event) {
                              return _vm.downloadFile(_vm.dialogs.view.file)
                            }
                          }
                        },
                        [
                          _vm._v(
                            "\n                        Download\n                        "
                          ),
                          _c("v-icon", { attrs: { right: "", dark: "" } }, [
                            _vm._v("file_download")
                          ])
                        ],
                        1
                      )
                    ],
                    1
                  ),
                  _vm._v(" "),
                  _c(
                    "v-toolbar-items",
                    [
                      _c(
                        "v-btn",
                        {
                          attrs: { dark: "", text: "" },
                          nativeOn: {
                            click: function($event) {
                              return _vm.trash(_vm.dialogs.view.file)
                            }
                          }
                        },
                        [
                          _vm._v(
                            "\n                        Delete\n                        "
                          ),
                          _c("v-icon", { attrs: { right: "", dark: "" } }, [
                            _vm._v("delete")
                          ])
                        ],
                        1
                      )
                    ],
                    1
                  )
                ],
                1
              ),
              _vm._v(" "),
              _c("v-card-text", [
                _c("div", { staticClass: "file_view_popup" }, [
                  _c(
                    "div",
                    { staticClass: "file_view_popup_link" },
                    [
                      _c("v-text-field", {
                        attrs: {
                          text: "",
                          disabled: "",
                          value: _vm.getFullUrl(_vm.dialogs.view.file)
                        }
                      })
                    ],
                    1
                  ),
                  _vm._v(" "),
                  _c("img", {
                    attrs: { src: _vm.getFullUrl(_vm.dialogs.view.file) }
                  })
                ])
              ])
            ],
            1
          )
        ],
        1
      )
    ],
    1
  )
}
var staticRenderFns = []
render._withStripped = true



/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/admin/dashboard/Home.vue?vue&type=template&id=e7659250&":
/*!************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/admin/dashboard/Home.vue?vue&type=template&id=e7659250& ***!
  \************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "render", function() { return render; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return staticRenderFns; });
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "div",
    { staticClass: "page_wrap_vue pa-3" },
    [
      _c("h2", [_vm._v("Dashboard")]),
      _vm._v(" "),
      _c(
        "v-row",
        { attrs: { "no-gutters": "" } },
        _vm._l(3, function(n) {
          return _c(
            "v-col",
            { key: n, attrs: { cols: "12", sm: "4" } },
            [
              _c(
                "v-card",
                { staticClass: "pa-2", attrs: { outlined: "", tile: "" } },
                [_c("comingsoonblock")],
                1
              )
            ],
            1
          )
        }),
        1
      )
    ],
    1
  )
}
var staticRenderFns = []
render._withStripped = true



/***/ }),

/***/ "./resources/js/admin/ComingSoon.vue":
/*!*******************************************!*\
  !*** ./resources/js/admin/ComingSoon.vue ***!
  \*******************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _ComingSoon_vue_vue_type_template_id_dd26360c_scoped_true___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./ComingSoon.vue?vue&type=template&id=dd26360c&scoped=true& */ "./resources/js/admin/ComingSoon.vue?vue&type=template&id=dd26360c&scoped=true&");
/* harmony import */ var _ComingSoon_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./ComingSoon.vue?vue&type=script&lang=js& */ "./resources/js/admin/ComingSoon.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _ComingSoon_vue_vue_type_style_index_0_id_dd26360c_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./ComingSoon.vue?vue&type=style&index=0&id=dd26360c&scoped=true&lang=css& */ "./resources/js/admin/ComingSoon.vue?vue&type=style&index=0&id=dd26360c&scoped=true&lang=css&");
/* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");






/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__["default"])(
  _ComingSoon_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _ComingSoon_vue_vue_type_template_id_dd26360c_scoped_true___WEBPACK_IMPORTED_MODULE_0__["render"],
  _ComingSoon_vue_vue_type_template_id_dd26360c_scoped_true___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  "dd26360c",
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/admin/ComingSoon.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/admin/ComingSoon.vue?vue&type=script&lang=js&":
/*!********************************************************************!*\
  !*** ./resources/js/admin/ComingSoon.vue?vue&type=script&lang=js& ***!
  \********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_ComingSoon_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../node_modules/babel-loader/lib??ref--4-0!../../../node_modules/vue-loader/lib??vue-loader-options!./ComingSoon.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/admin/ComingSoon.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_ComingSoon_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/admin/ComingSoon.vue?vue&type=style&index=0&id=dd26360c&scoped=true&lang=css&":
/*!****************************************************************************************************!*\
  !*** ./resources/js/admin/ComingSoon.vue?vue&type=style&index=0&id=dd26360c&scoped=true&lang=css& ***!
  \****************************************************************************************************/
/*! no static exports found */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_style_loader_index_js_node_modules_css_loader_index_js_ref_7_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_7_2_node_modules_vue_loader_lib_index_js_vue_loader_options_ComingSoon_vue_vue_type_style_index_0_id_dd26360c_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../node_modules/style-loader!../../../node_modules/css-loader??ref--7-1!../../../node_modules/vue-loader/lib/loaders/stylePostLoader.js!../../../node_modules/postcss-loader/src??ref--7-2!../../../node_modules/vue-loader/lib??vue-loader-options!./ComingSoon.vue?vue&type=style&index=0&id=dd26360c&scoped=true&lang=css& */ "./node_modules/style-loader/index.js!./node_modules/css-loader/index.js?!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/admin/ComingSoon.vue?vue&type=style&index=0&id=dd26360c&scoped=true&lang=css&");
/* harmony import */ var _node_modules_style_loader_index_js_node_modules_css_loader_index_js_ref_7_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_7_2_node_modules_vue_loader_lib_index_js_vue_loader_options_ComingSoon_vue_vue_type_style_index_0_id_dd26360c_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_style_loader_index_js_node_modules_css_loader_index_js_ref_7_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_7_2_node_modules_vue_loader_lib_index_js_vue_loader_options_ComingSoon_vue_vue_type_style_index_0_id_dd26360c_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0__);
/* harmony reexport (unknown) */ for(var __WEBPACK_IMPORT_KEY__ in _node_modules_style_loader_index_js_node_modules_css_loader_index_js_ref_7_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_7_2_node_modules_vue_loader_lib_index_js_vue_loader_options_ComingSoon_vue_vue_type_style_index_0_id_dd26360c_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0__) if(__WEBPACK_IMPORT_KEY__ !== 'default') (function(key) { __webpack_require__.d(__webpack_exports__, key, function() { return _node_modules_style_loader_index_js_node_modules_css_loader_index_js_ref_7_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_7_2_node_modules_vue_loader_lib_index_js_vue_loader_options_ComingSoon_vue_vue_type_style_index_0_id_dd26360c_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0__[key]; }) }(__WEBPACK_IMPORT_KEY__));
 /* harmony default export */ __webpack_exports__["default"] = (_node_modules_style_loader_index_js_node_modules_css_loader_index_js_ref_7_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_7_2_node_modules_vue_loader_lib_index_js_vue_loader_options_ComingSoon_vue_vue_type_style_index_0_id_dd26360c_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0___default.a); 

/***/ }),

/***/ "./resources/js/admin/ComingSoon.vue?vue&type=template&id=dd26360c&scoped=true&":
/*!**************************************************************************************!*\
  !*** ./resources/js/admin/ComingSoon.vue?vue&type=template&id=dd26360c&scoped=true& ***!
  \**************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_ComingSoon_vue_vue_type_template_id_dd26360c_scoped_true___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../node_modules/vue-loader/lib??vue-loader-options!./ComingSoon.vue?vue&type=template&id=dd26360c&scoped=true& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/admin/ComingSoon.vue?vue&type=template&id=dd26360c&scoped=true&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_ComingSoon_vue_vue_type_template_id_dd26360c_scoped_true___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_ComingSoon_vue_vue_type_template_id_dd26360c_scoped_true___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ }),

/***/ "./resources/js/admin/caserecording/components/FileLists.vue":
/*!*******************************************************************!*\
  !*** ./resources/js/admin/caserecording/components/FileLists.vue ***!
  \*******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _FileLists_vue_vue_type_template_id_3398ee6c_scoped_true___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./FileLists.vue?vue&type=template&id=3398ee6c&scoped=true& */ "./resources/js/admin/caserecording/components/FileLists.vue?vue&type=template&id=3398ee6c&scoped=true&");
/* harmony import */ var _FileLists_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./FileLists.vue?vue&type=script&lang=js& */ "./resources/js/admin/caserecording/components/FileLists.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _FileLists_vue_vue_type_style_index_0_id_3398ee6c_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./FileLists.vue?vue&type=style&index=0&id=3398ee6c&scoped=true&lang=css& */ "./resources/js/admin/caserecording/components/FileLists.vue?vue&type=style&index=0&id=3398ee6c&scoped=true&lang=css&");
/* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");






/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__["default"])(
  _FileLists_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _FileLists_vue_vue_type_template_id_3398ee6c_scoped_true___WEBPACK_IMPORTED_MODULE_0__["render"],
  _FileLists_vue_vue_type_template_id_3398ee6c_scoped_true___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  "3398ee6c",
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/admin/caserecording/components/FileLists.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/admin/caserecording/components/FileLists.vue?vue&type=script&lang=js&":
/*!********************************************************************************************!*\
  !*** ./resources/js/admin/caserecording/components/FileLists.vue?vue&type=script&lang=js& ***!
  \********************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_FileLists_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../node_modules/vue-loader/lib??vue-loader-options!./FileLists.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/admin/caserecording/components/FileLists.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_FileLists_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/admin/caserecording/components/FileLists.vue?vue&type=style&index=0&id=3398ee6c&scoped=true&lang=css&":
/*!****************************************************************************************************************************!*\
  !*** ./resources/js/admin/caserecording/components/FileLists.vue?vue&type=style&index=0&id=3398ee6c&scoped=true&lang=css& ***!
  \****************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_style_loader_index_js_node_modules_css_loader_index_js_ref_7_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_7_2_node_modules_vue_loader_lib_index_js_vue_loader_options_FileLists_vue_vue_type_style_index_0_id_3398ee6c_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/style-loader!../../../../../node_modules/css-loader??ref--7-1!../../../../../node_modules/vue-loader/lib/loaders/stylePostLoader.js!../../../../../node_modules/postcss-loader/src??ref--7-2!../../../../../node_modules/vue-loader/lib??vue-loader-options!./FileLists.vue?vue&type=style&index=0&id=3398ee6c&scoped=true&lang=css& */ "./node_modules/style-loader/index.js!./node_modules/css-loader/index.js?!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/admin/caserecording/components/FileLists.vue?vue&type=style&index=0&id=3398ee6c&scoped=true&lang=css&");
/* harmony import */ var _node_modules_style_loader_index_js_node_modules_css_loader_index_js_ref_7_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_7_2_node_modules_vue_loader_lib_index_js_vue_loader_options_FileLists_vue_vue_type_style_index_0_id_3398ee6c_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_style_loader_index_js_node_modules_css_loader_index_js_ref_7_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_7_2_node_modules_vue_loader_lib_index_js_vue_loader_options_FileLists_vue_vue_type_style_index_0_id_3398ee6c_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0__);
/* harmony reexport (unknown) */ for(var __WEBPACK_IMPORT_KEY__ in _node_modules_style_loader_index_js_node_modules_css_loader_index_js_ref_7_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_7_2_node_modules_vue_loader_lib_index_js_vue_loader_options_FileLists_vue_vue_type_style_index_0_id_3398ee6c_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0__) if(__WEBPACK_IMPORT_KEY__ !== 'default') (function(key) { __webpack_require__.d(__webpack_exports__, key, function() { return _node_modules_style_loader_index_js_node_modules_css_loader_index_js_ref_7_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_7_2_node_modules_vue_loader_lib_index_js_vue_loader_options_FileLists_vue_vue_type_style_index_0_id_3398ee6c_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0__[key]; }) }(__WEBPACK_IMPORT_KEY__));
 /* harmony default export */ __webpack_exports__["default"] = (_node_modules_style_loader_index_js_node_modules_css_loader_index_js_ref_7_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_7_2_node_modules_vue_loader_lib_index_js_vue_loader_options_FileLists_vue_vue_type_style_index_0_id_3398ee6c_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0___default.a); 

/***/ }),

/***/ "./resources/js/admin/caserecording/components/FileLists.vue?vue&type=template&id=3398ee6c&scoped=true&":
/*!**************************************************************************************************************!*\
  !*** ./resources/js/admin/caserecording/components/FileLists.vue?vue&type=template&id=3398ee6c&scoped=true& ***!
  \**************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_FileLists_vue_vue_type_template_id_3398ee6c_scoped_true___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../node_modules/vue-loader/lib??vue-loader-options!./FileLists.vue?vue&type=template&id=3398ee6c&scoped=true& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/admin/caserecording/components/FileLists.vue?vue&type=template&id=3398ee6c&scoped=true&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_FileLists_vue_vue_type_template_id_3398ee6c_scoped_true___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_FileLists_vue_vue_type_template_id_3398ee6c_scoped_true___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ }),

/***/ "./resources/js/admin/dashboard/Home.vue":
/*!***********************************************!*\
  !*** ./resources/js/admin/dashboard/Home.vue ***!
  \***********************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _Home_vue_vue_type_template_id_e7659250___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./Home.vue?vue&type=template&id=e7659250& */ "./resources/js/admin/dashboard/Home.vue?vue&type=template&id=e7659250&");
/* harmony import */ var _Home_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./Home.vue?vue&type=script&lang=js& */ "./resources/js/admin/dashboard/Home.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _Home_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _Home_vue_vue_type_template_id_e7659250___WEBPACK_IMPORTED_MODULE_0__["render"],
  _Home_vue_vue_type_template_id_e7659250___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/admin/dashboard/Home.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/admin/dashboard/Home.vue?vue&type=script&lang=js&":
/*!************************************************************************!*\
  !*** ./resources/js/admin/dashboard/Home.vue?vue&type=script&lang=js& ***!
  \************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Home_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../node_modules/babel-loader/lib??ref--4-0!../../../../node_modules/vue-loader/lib??vue-loader-options!./Home.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/admin/dashboard/Home.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Home_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/admin/dashboard/Home.vue?vue&type=template&id=e7659250&":
/*!******************************************************************************!*\
  !*** ./resources/js/admin/dashboard/Home.vue?vue&type=template&id=e7659250& ***!
  \******************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Home_vue_vue_type_template_id_e7659250___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../node_modules/vue-loader/lib??vue-loader-options!./Home.vue?vue&type=template&id=e7659250& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/admin/dashboard/Home.vue?vue&type=template&id=e7659250&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Home_vue_vue_type_template_id_e7659250___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Home_vue_vue_type_template_id_e7659250___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ }),

/***/ "./resources/js/admin/router.js":
/*!**************************************!*\
  !*** ./resources/js/admin/router.js ***!
  \**************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! vue */ "./node_modules/vue/dist/vue.common.js");
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(vue__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var vue_router__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! vue-router */ "./node_modules/vue-router/dist/vue-router.esm.js");
/* harmony import */ var _common_Store__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../common/Store */ "./resources/js/common/Store.js");



vue__WEBPACK_IMPORTED_MODULE_0___default.a.use(vue_router__WEBPACK_IMPORTED_MODULE_1__["default"]);
var router = new vue_router__WEBPACK_IMPORTED_MODULE_1__["default"]({
  routes: [{
    path: '/',
    redirect: '/dashboard'
  }, {
    name: 'dashboard',
    path: '/dashboard',
    component: __webpack_require__(/*! ./dashboard/Home */ "./resources/js/admin/dashboard/Home.vue")
  }, {
    name: 'caserecording.list',
    path: '/caserecording',
    component: __webpack_require__(/*! ./caserecording/components/FileLists.vue */ "./resources/js/admin/caserecording/components/FileLists.vue")
  }
  /*{
      name: 'recordingbrowser.list',
      path: '/recordingBrowser',
      component: require('./recordingbrowser/Home'),
  },
  {
      path: '/users',
      component: require('./users/Users'),
      children: [
          {
              path:'/',
              name:'users.list',
              component: require('./users/components/UserLists')
          },
          {
              path:'create',
              name:'users.create',
              component: require('./users/components/UserFormAdd')
          },
          {
              path:'edit/:id',
              name:'users.edit',
              component: require('./users/components/UserFormEdit'),
              props: (route) => ({propUserId: route.params.id}),
          },
          {
              path:'groups',
              name:'users.groups.list',
              component: require('./users/components/GroupLists')
          },
          {
              path:'groups/create',
              name:'users.groups.create',
              component: require('./users/components/GroupFromAdd')
          },
          {
              path:'groups/edit/:id',
              name:'users.groups.edit',
              component: require('./users/components/GroupFromEdit'),
              props: (route) => ({propGroupId: route.params.id}),
          },
          {
              path:'permissions',
              name:'users.permissions.list',
              component: require('./users/components/PermissionLists')
          },
          {
              path:'permissions/create',
              name:'users.permissions.create',
              component: require('./users/components/PermissionFormAdd')
          },
          {
              path:'permissions/edit/:id',
              name:'users.permissions.edit',
              component: require('./users/components/PermissionFormEdit'),
              props: (route) => ({propPermissionId: route.params.id}),
          },
      ]
  },
  {
      name: 'files',
      path: '/files',
      component: require('./files/Files'),
  },
  {
      name: 'settings',
      path: '/settings',
      component: require('./settings/Settings'),
  }*/
  ]
});
router.beforeEach(function (to, from, next) {
  _common_Store__WEBPACK_IMPORTED_MODULE_2__["default"].commit('showLoader');
  next();
});
router.afterEach(function (to, from) {
  setTimeout(function () {
    _common_Store__WEBPACK_IMPORTED_MODULE_2__["default"].commit('hideLoader');
  }, 1000);
});
/* harmony default export */ __webpack_exports__["default"] = (router);

/***/ }),

/***/ "./resources/js/common/Store.js":
/*!**************************************!*\
  !*** ./resources/js/common/Store.js ***!
  \**************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! vue */ "./node_modules/vue/dist/vue.common.js");
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(vue__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var vuex__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! vuex */ "./node_modules/vuex/dist/vuex.esm.js");


vue__WEBPACK_IMPORTED_MODULE_0___default.a.use(vuex__WEBPACK_IMPORTED_MODULE_1__["default"]);
/* harmony default export */ __webpack_exports__["default"] = (new vuex__WEBPACK_IMPORTED_MODULE_1__["default"].Store({
  state: {
    // breadcrumbs
    breadcrumbs: [],
    // loader
    showLoader: false,
    // snackbar
    showSnackbar: false,
    snackbarMessage: '',
    snackbarColor: '',
    snackbarDuration: 3000,
    // dialog
    dialogShow: false,
    dialogType: '',
    dialogTitle: '',
    dialogMessage: '',
    dialogIcon: null,
    dialogOkCb: function dialogOkCb() {},
    dialogCancelCb: function dialogCancelCb() {}
  },
  mutations: {
    // breadcrumbs
    setBreadcrumbs: function setBreadcrumbs(state, items) {
      items.unshift({
        label: 'Home',
        to: {
          name: 'dashboard'
        }
      });
      state.breadcrumbs = items;
    },
    // loader
    showLoader: function showLoader(state) {//state.showLoader = true
    },
    hideLoader: function hideLoader(state) {
      state.showLoader = false;
    },
    // snackbar
    showSnackbar: function showSnackbar(state, data) {
      state.snackbarDuration = data.duration || 3000;
      state.snackbarMessage = data.message || 'No message.';
      state.snackbarColor = data.color || 'info';
      state.showSnackbar = true;
    },
    hideSnackbar: function hideSnackbar(state) {
      state.showSnackbar = false;
    },
    // dialog
    showDialog: function showDialog(state, data) {
      state.dialogType = data.type || 'confirm';
      state.dialogTitle = data.title;
      state.dialogMessage = data.message;
      state.dialogIcon = data.icon || null;

      state.dialogOkCb = data.okCb || function () {};

      state.dialogCancelCb = data.cancelCb || function () {};

      state.dialogShow = true;
    },
    hideDialog: function hideDialog(state) {
      state.dialogShow = false;
    },
    dialogOk: function dialogOk(state) {
      state.dialogOkCb();
      state.dialogShow = false;
    },
    dialogCancel: function dialogCancel(state) {
      state.dialogCancelCb();
      state.dialogShow = false;
    }
  },
  getters: {
    // get breadcrumbs
    getBreadcrumbs: function getBreadcrumbs(state) {
      return state.breadcrumbs;
    },
    // loader
    showLoader: function showLoader(state) {
      return state.showLoader;
    },
    // snackbar
    showSnackbar: function showSnackbar(state) {
      return state.showSnackbar;
    },
    snackbarMessage: function snackbarMessage(state) {
      return state.snackbarMessage;
    },
    snackbarColor: function snackbarColor(state) {
      return state.snackbarColor;
    },
    snackbarDuration: function snackbarDuration(state) {
      return state.snackbarDuration;
    },
    // dialog
    showDialog: function showDialog(state) {
      return state.dialogShow;
    },
    dialogType: function dialogType(state) {
      return state.dialogType;
    },
    dialogTitle: function dialogTitle(state) {
      return state.dialogTitle;
    },
    dialogMessage: function dialogMessage(state) {
      return state.dialogMessage;
    },
    dialogIcon: function dialogIcon(state) {
      return state.dialogIcon;
    }
  }
}));

/***/ })

}]);