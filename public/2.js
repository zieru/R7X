(window["webpackJsonp"] = window["webpackJsonp"] || []).push([[2],{

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