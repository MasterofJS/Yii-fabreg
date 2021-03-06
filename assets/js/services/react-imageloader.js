'use strict';

Object.defineProperty(exports, '__esModule', {
  value: true
});

var _createClass = (function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ('value' in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; })();

var _get = function get(_x, _x2, _x3) { var _again = true; _function: while (_again) { var object = _x, property = _x2, receiver = _x3; desc = parent = getter = undefined; _again = false; var desc = Object.getOwnPropertyDescriptor(object, property); if (desc === undefined) { var parent = Object.getPrototypeOf(object); if (parent === null) { return undefined; } else { _x = parent; _x2 = property; _x3 = receiver; _again = true; continue _function; } } else if ('value' in desc) { return desc.value; } else { var getter = desc.get; if (getter === undefined) { return undefined; } return getter.call(receiver); } } };

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { 'default': obj }; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError('Cannot call a class as a function'); } }

function _inherits(subClass, superClass) { if (typeof superClass !== 'function' && superClass !== null) { throw new TypeError('Super expression must either be null or a function, not ' + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) subClass.__proto__ = superClass; }

var _react = React;

var _react2 = _interopRequireDefault(_react);

var PropTypes = _react2['default'].PropTypes;
var span = _react2['default'].DOM.span;

var Status = {
  PENDING: 'pending',
  LOADING: 'loading',
  LOADED: 'loaded',
  FAILED: 'failed'
};

var ImageLoader = (function (_React$Component) {
  function ImageLoader(props) {
    _classCallCheck(this, ImageLoader);

    _get(Object.getPrototypeOf(ImageLoader.prototype), 'constructor', this).call(this, props);
    this.state = { status: props.src ? Status.LOADING : Status.PENDING };
  }

  _inherits(ImageLoader, _React$Component);

  _createClass(ImageLoader, [{
    key: 'componentDidMount',
    value: function componentDidMount() {
      if (this.state.status === Status.LOADING) {
        this.createLoader();
      }
    }
  }, {
    key: 'componentWillReceiveProps',
    value: function componentWillReceiveProps(nextProps) {
      if (this.props.src !== nextProps.src) {
        this.setState({
          status: nextProps.src ? Status.LOADING : Status.PENDING
        });
      }
    }
  }, {
    key: 'componentDidUpdate',
    value: function componentDidUpdate() {
      if (this.state.status === Status.LOADING && !this.img) {
        this.createLoader();
      }
    }
  }, {
    key: 'componentWillUnmount',
    value: function componentWillUnmount() {
      this.destroyLoader();
    }
  }, {
    key: 'getClassName',
    value: function getClassName() {
      var className = 'imageloader ' + this.state.status;
      if (this.props.className) className = '' + className + ' ' + this.props.className;
      return className;
    }
  }, {
    key: 'createLoader',
    value: function createLoader() {
      this.destroyLoader(); // We can only have one loader at a time.

      this.img = new Image();
      this.img.onload = this.handleLoad.bind(this);
      this.img.onerror = this.handleError.bind(this);
      this.img.src = this.props.src;
    }
  }, {
    key: 'destroyLoader',
    value: function destroyLoader() {
      if (this.img) {
        this.img.onload = null;
        this.img.onerror = null;
        this.img = null;
      }
    }
  }, {
    key: 'handleLoad',
    value: function handleLoad(event) {
      this.destroyLoader();
      this.setState({ status: Status.LOADED });

      if (this.props.onLoad) this.props.onLoad(event);
    }
  }, {
    key: 'handleError',
    value: function handleError(error) {
      this.destroyLoader();
      this.setState({ status: Status.FAILED });

      if (this.props.onError) this.props.onError(error);
    }
  }, {
    key: 'renderImg',
    value: function renderImg() {
      var _props2 = this.props;
      var src = _props2.src;
      var imgProps = _props2.imgProps;

      var props = { src: src };

      for (var k in imgProps) {
        if (imgProps.hasOwnProperty(k)) {
          props[k] = imgProps[k];
        }
      }

      return _react2['default'].createElement('img', props);
    }
  }, {
    key: 'render',
    value: function render() {
      var _props;

      var wrapperProps = {
        className: this.getClassName()
      };

      if (this.props.style) {
        wrapperProps.style = this.props.style;
      }

      var wrapperArgs = [wrapperProps];

      switch (this.state.status) {
        case Status.LOADED:
          wrapperArgs.push(this.renderImg());
          break;

        case Status.FAILED:
          if (this.props.children) wrapperArgs.push(this.props.children);
          break;

        default:
          if (this.props.preloader) wrapperArgs.push(this.props.preloader());
          break;
      }

      return (_props = this.props).wrapper.apply(_props, wrapperArgs);
    }
  }], [{
    key: 'propTypes',
    value: {
      wrapper: PropTypes.func,
      className: PropTypes.string,
      style: PropTypes.object,
      preloader: PropTypes.func,
      src: PropTypes.string,
      onLoad: PropTypes.func,
      onError: PropTypes.func,
      imgProps: PropTypes.object
    },
    enumerable: true
  }, {
    key: 'defaultProps',
    value: {
      wrapper: span
    },
    enumerable: true
  }]);

  return ImageLoader;
})(_react2['default'].Component);

exports['default'] = ImageLoader;
module.exports = exports['default'];
