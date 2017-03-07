import { ROOT, API } from '../../modules/config';
const ReactScriptLoaderMixin = require('../../services/ReactScriptLoader.js').ReactScriptLoaderMixin;
const Fileupload = require('../sign_up/fileupload.jsx');

const BGUpload = React.createClass({

    mixins: [ReactScriptLoaderMixin],

    getInitialState () {
        return {
            scriptLoading: true,
            scriptLoadError: false,
            fileupload: {
                classN: null,
                message: null
            }
        }
    },

    getScriptURL () {

        return `${ROOT}dist/js/pages/sign_up.min.js`

    },

    onScriptError () {
        this.setState({scriptLoading: false, scriptLoadError: true});
    },

    onScriptLoaded () {
        var self = this;
        var $fileuploadWrap;
        var $fileupload;

        this.setState({scriptLoading: false});

        $fileupload = $('#fileupload_bg');
        $fileuploadWrap = $('#header_');

        $fileupload.fileupload({

                //url: '//jquery-file-upload.appspot.com/',
                url: `${API}/upload`,
                paramName: 'photo',
                formData: [
                    {
                        'name': 'scenario',
                        'value': 'cover'
                    }
                ],
                dropZone: $('.dropzone'),
                acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i,
                maxFileSize: 20000,
                done: function (e, data) {

                    if (data.result.files[0].error) {
                        fileuploadMessage(data.result.files[0].error);
                        $fileuploadWrap.removeClass('loading');
                        return;
                    }

                    self.setState({
                        'token': data.result.files[0].secureName
                    });

                    $('<img/>')
                        .one('load', function () {

                            self.props.cbChangeCover.apply(this, [data.result.files[0].downloadUrl, data.result.files[0].secureName]);
                            $fileuploadWrap.removeClass('loading');

                        })
                        .attr('src', data.result.files[0].downloadUrl);


                },
                fail: function (e, data) {
                    fileuploadMessage('Server Error');
                    $fileuploadWrap.removeClass('loading');
                }
            })
            .on('fileuploadadd', function (e, data) {
                var fileType, allowdtypes;

                fileuploadMessage(null);
                fileType = data.files[0].name.split('.').pop(),
                    allowdtypes = 'jpeg,jpg,png,gif';
                if (allowdtypes.indexOf(fileType) < 0) {
                    fileuploadMessage('Tipo de arquivo inválido');
                    return false;
                } else {
                    $fileuploadWrap.addClass('loading');
                }
            });

        function fileuploadMessage(message, type) {
            self.props.onUpdate({classN: type || 'alert', message: message});
        }


    },

    render () {

        return (
            <form>
                <div className="fileupload_ bg_ dropzone fileinput-button">

                    <div className="action_">
                        <i className="icon-camera"></i>
                        <span className="popover_">Trocar Capa</span>
                    </div>

                    <input id="fileupload_bg" type="file" name="files[]"/>

                </div>
            </form>
        );
    }
});

const Message = React.createClass({
    getInitialState () {
        return {showResults: false};
    },
    onClick (e) {
        this.setState({showResults: true});
        e.preventDefault();
    },
    render () {

        var className;
        if (null === this.props.message || this.state.showResults) {
            className = 'hidden';
            this.state.showResults = false;
        } else {
            className = this.props.class + " alert-box";
        }
        return (
            <div className={className}>
                {this.props.message}
                <a href="#" onClick={this.onClick} className="close">&times;</a>
            </div>
        );
    }
});

export default React.createClass({

    getInitialState () {
        return {
            classN: null,
            message: null,
            user: this.props.user,
            avatar: this.props.user._links ? this.props.user._links.avatar.href + `?${Date.now()}` : null,
            tokenAvatar: null,
            cover: this.props.user._links && this.props.user._links.cover ? this.props.user._links.cover.href : null,
            tokenCover: null,
            saveButton: false,
            reload: false
        }
    },


    componentWillMount(){
        this.reload = Date.now();
    },

    render () {
        const user = this.state.user;
        let classN = this.state.classN;
        let message = this.state.message;
        let bg = null;
        let personal = null;
        let messageBox = null;
        const isOwner = (auth.loggedIn() && (this.state.user.username === APP.state.user.username));

        if (isOwner) {

            personal = (<form>
                <Fileupload
                    icon={true}
                    avatar={ this.state.avatar || (this.props.user._links && this.props.user._links.avatar.href) + `?${this.reload}` }
                    cbChangeAvatar={this.cbChangeAvatar}
                />
            </form>);

            messageBox = <Message class={classN} message={message}/>;

            bg = (
                <div className="bg_personal_ column medium-2">
                    <BGUpload
                        cbChangeCover={ this.cbChangeCover }
                        onUpdate={ this.handleUpload }
                        reload={this.state.reload}
                    />
                </div>
            );

        } else {
            bg = (
                <div className="bg_personal_ column medium-2">&nbsp;</div>
            );
            personal = (
                <div className="row">
                    <div className="column">
                        <div className="fileupload_ dropzone fileinput-button">
                            <div
                                className="picture_"
                                style={{
                                    backgroundImage: `url(${user._links.avatar.href}?${this.reload})`
                                }}
                            >
                            </div>
                        </div>
                    </div>
                </div>
            );

        }

        return (

            <header
                className="main_head_"
                id="header_"
                style={{
                    backgroundImage: this.state.cover || user._links.cover ? `url(${ this.state.cover || user._links.cover.href }?${ this.reload })` : null
                }}>
                <div className="row">
                    {bg}
                    <div className={
                        classNames({
                            "personal_" : true,
                            "columns": true,
                            "medium-8": true,
                            "end": true,
                            "small-centered": isOwner,
                            "medium-uncentered": isOwner
                        })
                    }>
                        <div className="img_">

                            {personal}

                        </div>
                        <h1 className="name_">{user.first_name}&nbsp;{user.last_name}</h1>
                        <h2 className="subtitle text-center">{user.about || 'Minha coleção'}</h2>
                    </div>
                </div>

                <div className="loader_">
                    <i className="icon-spin6 animate-spin"></i>
                </div>

                {messageBox}

                {isOwner && this.state.saveButton && (
                    <button onClick={this._saveChanges} type="button" className="button save_changes_">Salvar</button>)}
            </header>

        );
    },

    handleUpload (o) {

        this.setState({
            classN: o.classN,
            message: o.message
        });

    },

    cbChangeAvatar(newAvatar, token){

        this.setState({
            avatar: newAvatar,
            tokenAvatar: token,
            saveButton: true,
            reload: true
        });

    },

    cbChangeCover(newAvatar, token){

        this.setState({
            cover: newAvatar,
            tokenCover: token,
            saveButton: true
        });

    },

    _saveChanges(){
        const res = {};


        if (this.state.tokenCover) {
            res.cover = this.state.tokenCover;
        }
        if (this.state.tokenAvatar) {
            res.avatar = this.state.tokenAvatar;
        }

        $.ajax({
            type: 'put',
            data: res,
            url: `${ API }/users/me`,//${this.state.user.username}
            success: function (respond) {

                if (Array.isArray(respond)) {
                    this.setState({
                        classN: 'alert',
                        message: respond.files[0].error
                    });

                    return;
                }

                this.setState({
                    saveButton: false,
                    tokenCover: null,
                    tokenAvatar: null,
                    reload: true
                });

                auth.setUser(respond);

            }.bind(this),
            error: function (error) {
                console.warn(error);
                this.setState({
                    classN: 'alert',
                    message: error.statusText
                });
            }.bind(this)
        });
    }
});
