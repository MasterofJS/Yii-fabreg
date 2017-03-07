import AutoForm from "react-auto-form";
import * as router from "react-router";
import {ROOT, API} from "../../modules/config";
import isImage from "../../services/isImageLink";
const ReactScriptLoaderMixin = require('../../services/ReactScriptLoader.js').ReactScriptLoaderMixin;

export default React.createClass({

    mixins: [ReactScriptLoaderMixin, router.History],

    getInitialState() {
        return {
            submittedData: null,
            scriptLoading: true,
            scriptLoadError: false,
            file: null,
            validation: null,
            validationMessage: null,
            validationType: null,
            image: null,
            tokenImage: null,
            linkValue: null
        }
    },

    getScriptURL () {

        return `${ROOT}dist/js/pages/sign_up.min.js`

    },

    onScriptError () {
        this.setState({scriptLoading: false, scriptLoadError: true});
    },

    fileuploadMessage(message, type = 'warning') {

        this.setState({
            validationMessage: message,
            validationType: type
        })
    },

    onScriptLoaded () {
        var self = this;
        var $fileuploadWrap;
        var $fileupload;


        this.setState({scriptLoading: false});

        $fileupload = $('#fileupload_post');
        $fileuploadWrap = $('.dropzone_post');

        $fileupload.fileupload({

                url: `${API}/upload`,
                paramName: 'photo',
                formData: [
                    {
                        'name': 'scenario',
                        'value': 'post'
                    }
                ],
                dropZone: $('.dropzone_post'),
                acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i,
                maxFileSize: 20000,
                done: function (e, data) {

                    if (data.result.files[0].error) {
                        self.fileuploadMessage(data.result.files[0].error);
                        $fileuploadWrap.removeClass('loading');
                        return;
                    }

                    self.setState({
                        tokenImage: data.result.files[0].secureName,
                        image: data.result.files[0].downloadUrl
                    });

                    $('<img/>')
                        .one('load', function () {
                            self.setState({
                                file: true
                            });
                            $fileuploadWrap.removeClass('loading');

                        })
                        .attr('src', data.result.files[0].downloadUrl);


                },
                fail: function (e, data) {
                    self.fileuploadMessage('erro de servidor');
                    $fileuploadWrap.removeClass('loading');
                }
            })
            .on('fileuploadadd', function (e, data) {
                var fileType, allowdtypes;

                self.fileuploadMessage(null);
                fileType = data.files[0].name.split('.').pop(),
                    allowdtypes = 'jpeg,jpg,png,gif,JPEG,JPG,PNG,GIF';
                if (allowdtypes.indexOf(fileType) < 0) {
                    self.fileuploadMessage('Tipo de arquivo inválido');
                    return false;
                } else {
                    $fileuploadWrap.addClass('loading');
                }
            });

        //function fileuploadMessage(message, type) {
        //self.props.onUpdate({classN: type || 'alert', message: message});
        //console.warn(type, message);
        //}


    },

    render () {
        return (
            <div className="modal_post_">
                {!this.state.file && <div ref="step_01">
                    <header className="head_">
                        <h1 className="title_">Poste algo engraçado</h1>
                        <h3 className="subtitle_">Escolha como prefere postar</h3>
                    </header>

                    <AutoForm onSubmit={this.handleSubmit} trimOnSubmit>

                        <div className="zone_ dropzone_post fileupload_ fileinput-button">
                            <div>
                                <img src={`${ ROOT }dist/images/drag_img.png`} alt="#"/>
                                <h5> Selecione ou arraste/solte a imagem aqui</h5>
                            </div>
                            <input id="fileupload_post" type="file" name="files[]"/>
                            <span className="preloader_">
                                <i className="icon-spin6 animate-spin"></i>
                            </span>
                        </div>

                        <div className="zone_ link_" onClick={this._hadleClickZoneLink}>

                            <img src={`${ ROOT }dist/images/link_img.png`} alt="#"/>

                            <h5>Adicione endereço/link URL</h5>

                            <div className="fieldgroup_">
                                <input
                                    ref="_link"
                                    type="text"
                                    name="link"
                                    placeholder="http://"
                                    value={this.state.linkValue}
                                    onChange={this._changeLinkValue}
                                />
                            </div>
                             <span className="preloader_">
                                <i className="icon-spin6 animate-spin"></i>
                            </span>

                        </div>

                        {
                            this.state.validationMessage &&
                            <div className={`alert-box ${this.state.validationType}`}>
                                {this.state.validationMessage}
                            </div>

                        }

                        <button type="submit">Próximo <i className="icon-down-open"></i></button>

                    </AutoForm>
                </div>}
                <div>

                    {
                        this.state.file &&
                        <ConfirmationPost
                            token={this.state.tokenImage}
                            image={this.state.image}
                            history={this.props.history}
                            handleClose={this.props.handleClose}
                            onPrev={this.onPrev}
                        />
                    }
                </div>

                {this.state.validation && <div className="validation_">{this.state.validation}</div>}

            </div>

        );
    },

    onDrop (files) {

        this.setState({
            validation: null,
            file: files[0].preview
        })
    },

    handleSubmit (e, submittedData) {
        const that = this;
        var $fileuploadWrap = $('.zone_.link_');

        e.preventDefault();
        if (submittedData.link) {

            if (Array.isArray(submittedData.link.match(isImage()))) {
                that.fileuploadMessage(null);
                $fileuploadWrap.addClass('loading');

            } else {
                that.fileuploadMessage('Link da imagem incorreto', 'alert');
                return;
            }


            $.ajax({
                url: `${ API }/upload`,
                type: 'post',
                data: {
                    'photo': submittedData.link,
                    'scenario': 'post'
                },
                success: function (data) {

                    if (data.files[0].error) {
                        that.fileuploadMessage(data.files[0].error);
                        $fileuploadWrap.removeClass('loading');
                        return;
                    }

                    that.setState({
                        tokenImage: data.files[0].secureName,
                        image: data.files[0].downloadUrl
                    });

                    $('<img/>')
                        .one('load', function () {
                            that.setState({
                                file: true
                            });
                            $fileuploadWrap.removeClass('loading');

                        })
                        .attr('src', data.files[0].downloadUrl);


                },
                error: function (error) {
                    console.warn(error);
                }
            });

            //return;
        }

    },

    _hadleClickZoneLink() {
        this.refs._link.focus();
        this.setState({validation: null})
    },

    _changeLinkValue(e){
        this.setState({
            linkValue: e.target.value
        })
    },

    onPrev(){
        this.setState({
            file: null,
            image: null,
            validation: null
        }, function () {
            this.onScriptLoaded();
        });
    }

});

const ConfirmationPost = React.createClass({

    mixins: [router.History],

    getInitialState () {
        return {
            submittedData: null,
            loaded: true,
            count: 140,
            text: null,
            validate: null,
            waitServer: false
        };
    },

    render () {
        return (
            <div className={`confirmation_ ${this.state.waitServer ? 'wait_server_' : ''}`}>
                <AutoForm onSubmit={this.handleSubmit} trimOnSubmit>

                    <h1 className="title_">Não esqueça de adicionar um título ao seu post</h1>

                    <div className="picture_">
                        <img src={this.props.image} alt="#"/>
                    </div>

                    <div className="in_" ref="description">
                        <textarea name="description"
                                  placeholder="Escreva aqui seu título"
                                  value={this.state.text}
                                  onChange={this._onChange}
                        />
                        <small className="error"></small>
                    </div>

                    <div className="row row_nsfw_">
                        <label>
                            <input type="checkbox" name="is_nsfw"/> Não Abrir No Trabalho (NANT)
                        </label>
                        <div className="right">{this.state.count}</div>
                    </div>

                    {this.state.validate && <div className="validation_">{this.state.validate}</div>}

                    <div className="row text-right foot_">
                        <button type="button" className="prev_" onClick={this.props.onPrev}>
                            <i className="icon-down-open"></i>
                            <span>Anterior</span>
                        </button>
                        <button type="submit">Postar</button>
                    </div>

                </AutoForm>

            </div>
        );
    },

    _onChange(e) {

        if (e.target.value.length > 140) {
            return;
        }
        this.setState({
            text: e.target.value,
            count: 140 - e.target.value.length
        });

    },

    handleSubmit (e, submittedData) {
        const that = this;
        e.preventDefault();
        if (submittedData.description.length < 4) {

            this.setState({
                validate: 'O título do post deve conter pelo menos 4 caracteres'
            });

            return false;
        }
        $.ajax({
            url: `${ API }/posts`,
            type: 'post',
            data: {
                photo: this.props.token,
                description: submittedData.description,
                is_nsfw: submittedData.is_nsfw ? '1' : '0'
            },
            beforeSend: function () {
                this.setState({waitServer: true});
            }.bind(this),
            success: function (respond) {
                if (Array.isArray(respond)) {

                    that.setState({
                        validate: respond[0].message,
                        waitServer: false
                    });

                    return;
                }

                APP.props.history.replaceState({}, `/posts/${respond.id}`);
                that.props.handleClose();

            },
            error: function (error) {
                console.warn(error);
                that.setState({
                    validate: error.responseText,
                    waitServer: false
                });
            }
        });

    }
});

