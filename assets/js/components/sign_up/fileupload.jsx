import React from 'react';
import { ROOT, API } from '../../modules/config';

const ReactScriptLoaderMixin = require('../../services/ReactScriptLoader.js').ReactScriptLoaderMixin;

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
                {this.props.message}<a href="#" onClick={this.onClick} className="close">&times;</a>
            </div>
        );
    }

});

export default React.createClass({
    
    mixins: [ReactScriptLoaderMixin],
    
    getInitialState () {
        return {
            scriptLoading: true,
            scriptLoadError: false,
            fileupload: {
                classN: null,
                message: null
            },
            token: null,
            avatars: null
        }
    },

    componentWillMount(){
        $.getJSON(`${ API }/media/default-avatars`, function (avatars) {
            if (this.isMounted()) {
                this.setState({
                    avatars
                });
                if (!this.props.avatar) {
                    this._loadRandomAvatar("0");
                }
            }
        }.bind(this));
    },

    getScriptURL () {
        return `${ ROOT }dist/js/pages/sign_up.min.js`
    },
    
    onScriptLoaded () {
        var self = this;
        var $fileuploadWrap;
        var $fileupload;

        this.setState({scriptLoading: false});

        $.support.cors = true;

        /* File upload */
        $fileupload = $('#fileupload');
        $fileuploadWrap = $('.fileupload_');
        $fileupload.fileupload({
                url: `${API}/upload`,
                paramName: 'photo',
                formData: [
                    {
                        'name': 'scenario',
                        'value': 'avatar'
                    }
                ],
                dropZone: $('.dropzone'),
                acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i,
                maxFileSize: 20000,

                done (e, data) {

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
                            
                            self.props.cbChangeAvatar.apply(null, [data.result.files[0].downloadUrl, data.result.files[0].secureName]);
                            $fileuploadWrap.removeClass('loading');
                            
                        })
                        .attr('src', data.result.files[0].downloadUrl);
                },

                fail (e, data) {
                    fileuploadMessage('Server Error');
                    $fileuploadWrap.removeClass('loading');
                }
            })
            .on('fileuploadadd', function (e, data) {
                var fileType, allowdtypes;
                
                fileuploadMessage(null);
                
                fileType = data.files[0].name.split('.').pop(),
                    allowdtypes = 'jpeg,jpg,png,JPEG,JPG,PNG';
                
                if (allowdtypes.indexOf(fileType) < 0) {
                    
                    fileuploadMessage('Tipo de arquivo inválido');
                    return false;
                    
                } else {
                    $fileuploadWrap.addClass('loading');
                }
            });
        
        $('body')
            .one('dragover', dragoverHandler)
            .on('drop', function () {
                $('.dropzone').removeClass('dropover');
                $('body').one('dragover', dragoverHandler);
            });
        
        function dragoverHandler() {
            $('.dropzone').addClass('dropover');
        }
        
        function fileuploadMessage(message, type) {
            self.setState({
                fileupload: {classN: type || 'alert', message: message}
            });
        }
    },
    
    onScriptError () {
        this.setState({scriptLoading: false, scriptLoadError: true});
    },

    render () {
        var classN = this.state.fileupload.classN,
            message = this.state.fileupload.message;
        return (
            <div className="row">
                <div className="column text-center">
                    
                    <div className="fileupload_ dropzone fileinput-button">
                        <div className="picture_"
                             style={{
                                backgroundImage: `url(${this.props.avatar})`
                            }}>
                            <i className="icon-spin6 animate-spin"></i>
                        </div>
                            <span className="action_">{this.props.icon ? (<i
                                className="icon-camera"></i>) : 'Carregar imagem'}</span>
                        <input id="fileupload" type="file" name="files[]"/>
                    </div>
                    
                    <input type="hidden" name="avatar" value={this.state.token}/>
                    
                    <a href="#"
                       role="button"
                       className="left random_btn_"
                       onClick={this._loadRandomAvatar}>
                        <i className="icon-arrows-cw"></i>Mudar imagem
                    </a>
                    
                    <Message class={classN} message={message}/>
                </div>
            </div>
        );
    },
    
    _loadRandomAvatar(e){
        const avatars = this.state.avatars;
        let randomIndex = Math.floor(getRandomArbitrary(0, avatars.length + 0.01));
        if (typeof e === 'object') {
            e.preventDefault();
        } else if (e) {
            randomIndex = +e;
        }
        this.setState({
            token: `default:${avatars[randomIndex].name}`
        }, function () {
            this.props.cbChangeAvatar.apply(this, [avatars[randomIndex]._links.self.href, `default:${avatars[randomIndex].name}`])
        });

    }
});


function getRandomArbitrary(min, max) {
    return Math.random() * (max - min) + min;
}
