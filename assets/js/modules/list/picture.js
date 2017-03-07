import ImageLoader from '../../services/react-imageloader';
import ScrollMagic from 'scrollmagic';
import { isMobile } from '../../services/isiOS';

const controller = new ScrollMagic.Controller({
    globalSceneOptions: {
        triggerHook: 0.8
    }
});

const Preloader = function () {
    return (<div className="placehold_">
        <i className="icon-spin6 animate-spin"></i>
    </div>)
}

export default React.createClass({

    getInitialState () {
        return {
            loaded: false
        }
    },

    getDefaultProps () {
        return {
            type: 'image'
        }
    },

    render () {
        let content, classImg;
        if (this.props.type === 'gif') {
            classImg = classNames({
                'pic_': true,
                'gif_': true
            });

        } else if (this.props.type === 'image' || 'long_image') {

            classImg = classNames({
                'pic_': true,
                'loading_': !this.state.loaded,
                'less_': this.props.type === 'long_image'
            });

            content = (
                <ImageLoader
                    src={this.props.src}
                    preloader={Preloader}
                    onLoad={this.handleLoad}>
                    Image load failed!
                </ImageLoader>
            );

        }

        return (
            <div className={classImg}>
                {
                    this.props.withoutWrap && this.props.type !== 'gif' ?
                        content :
                        <Link to={this.props.link} target="_blank">
                            { content }
                            {
                                this.props.type === 'long_image' &&
                                <span className="less_link_">
                            <i className="icon-link-ext"></i>Ver Post Inteiro</span>
                            }
                        </Link>
                }
                {
                    (this.props.type === 'gif') &&
                    <Video src={this.props.links}/>
                }
            </div>
        );

    },

    handleLoad() {
        this.setState({
            loaded: true
        })
    }
});

const Video = React.createClass({

    getInitialState () {
        return {
            paused: true
        };
    },

    scene: null,

    video: null,

    isMobile: isMobile(),

    handlePause(){
        this.setState({
            paused: true
        });
    },

    handlePlay(){
        this.setState({
            paused: false
        });
    },

    componentDidMount() {
        const that = this;

        this.video = ReactDOM.findDOMNode(this.refs.video);

        this.video.addEventListener('pause', this.handlePause);
        this.video.addEventListener('play', this.handlePlay);


        if (!this.isMobile) {
            this.video = ReactDOM.findDOMNode(this.refs.video);

            this.scene = new ScrollMagic.Scene({
                triggerElement: this.video,
                duration: '130%'
            })
            //.addIndicators()
                .on("enter", function (event) {
                    that.video.play();
                    that.setState({
                        paused: false
                    });
                })
                .on("leave", function (event) {
                    that.video.pause();
                    that.setState({
                        paused: true
                    });
                })
                .addTo(controller);
        }
    },

    componentWillUnmount () {

        this.video.removeEventListener('pause', this.handlePause);
        this.video.removeEventListener('play', this.handlePlay);

        if (this.scene) {
            this.scene.destroy();
        }
    },

    render () {
        return (
            <div className="gif_wrap_">
                <video
                    preload="auto"
                    ref="video"
                    poster={`${this.props.src.photo.href}`}
                    loop={true}
                    muted=""
                    controls={ this.isiOS }
                >
                    <source src={`${this.props.src.video.mp4.href}`} type="video/mp4"/>
                    <source src={`${this.props.src.video.webm.href}`} type="video/webm"/>
                </video>
                {
                    //!this.isMobile &&
                    <div
                        className={classNames({
                        'control_': true,
                        'paused_': this.state.paused
                    })}
                        onClick={this._handleClick}
                    >
                    <span>GIF</span>
                </div>
                }
            </div>
        );
    },

    _handleClick(){
        if (this.state.paused) {
            this.video.play();
        } else {
            this.video.pause();
        }
    }

});

