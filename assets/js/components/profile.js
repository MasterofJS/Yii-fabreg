import React from 'react';

import Countries from '../modules/countrybuilder';

export default React.createClass({

    getInitialState () {
        return {
            gender: this.props.user !== 'guest' ? this.props.user.gender : '',
            yyyy: this.props.user.birthday ? +this.props.user.birthday.substring(0, 4) : '',
            mm: this.props.user.birthday ? +this.props.user.birthday.substring(5, 7) : '',
            dd: this.props.user.birthday ? this.props.user.birthday.substring(8, 10) : '',
            about: this.props.user.about || ''
        };
    },

    componentDidMount(){
        this.props.cbInit(this);
    },

    render () {
        //var require = this.props.requared;
        return (
            <div>
                <div className="row">
                    <div
                        className="medium-4 columns"
                        ref="gender">
                        <select

                            title="Gender"
                            name="gender"
                            onChange={this._onChangeGender}
                            value={ this.state.gender }
                            defaultValue={ this.props.user !== 'guest' ? this.props.user.gender : ''
                           }
                        >
                            <option value="">Sexo</option>
                            <option value="M">Masculino</option>
                            <option value="F">Feminino</option>
                        </select>
                        <small className="error">Broke.</small>
                    </div>
                </div>
                <div
                    className="row"
                    ref="birthday">
                    <div className="column">
                        <label>Dia de nascimento</label>
                    </div>
                    <div
                        className="medium-3 small-4 columns end data_valid_">
                        <input
                            onChange={this._onChangeDD}
                            value={ this.state.dd }
                            name="day_birthday"
                            //id="day_sign_up_"
                            type="number"
                            max="31"
                            placeholder="Dia"
                        />
                    </div>
                    <div className="medium-3 small-4 columns">
                        <select
                            //id="month_sign_up_"
                            name="month_birthday"
                            title="Mês"
                            onChange={this._onChangeMM}
                            value={ this.state.mm }
                            defaultValue={ this.state.mm }
                        >
                            <option value="" defaultChecked>Mês</option>
                            <option value="1">janeiro</option>
                            <option value="2">fevereiro</option>
                            <option value="3">março</option>
                            <option value="4">abril</option>
                            <option value="5">maio</option>
                            <option value="6">junho</option>
                            <option value="7">julho</option>
                            <option value="8">agosto</option>
                            <option value="9">septembro</option>
                            <option value="10">outubro</option>
                            <option value="11">novembro</option>
                            <option value="12">dezembro</option>
                        </select>
                    </div>
                    <div className="medium-3 small-4 columns end">
                        <select
                            //id="year_sign_up_"
                            name="year_birthday"
                            title="Ano"
                            onChange={this._onChangeYYYY}
                            value={ this.state.yyyy }
                            defaultValue={ this.state.yyyy }
                        >
                            <option value="">Ano</option>
                            {(function () {
                                var i, val;
                                let years = [];
                                const yyyy = new Date().getFullYear();
                                for (i = 0; i < 80; i++) {
                                    val = yyyy - i;
                                    years.push(<option key={i} value={val}>{val}</option>);
                                }
                                return years;
                            })()}
                        </select>
                    </div>
                    <div className="column">
                        <small className="error">Data inválida.</small>
                    </div>
                </div>
                <div className="row">
                    <div className="medium-6 columns" ref='counties'>

                        <Countries
                            country={ this.props.user.country }
                            id="country_sign_up_"
                            name="country"
                        />
                        <small className="error">Broke.</small>
                    </div>
                </div>
                <div className="row">
                    <div className="large-12 columns" ref="about">
                        <textarea
                            value={ this.state.about }
                            onChange={this._onChangeAbout}
                            name="about"
                            placeholder="Escreva algo sobre você..."></textarea>
                        <small className="error"></small>
                    </div>
                </div>
            </div>
        );
    },

    _onChangeGender (e){

        this.setState({
            gender: e.target.value
        });
    },
    _onChangeYYYY (e){

        this.setState({
            yyyy: e.target.value
        });
    },
    _onChangeMM (e){

        this.setState({
            mm: e.target.value
        });
    },
    _onChangeDD (e){

        this.setState({
            dd: e.target.value
        });
    },
    _onChangeAbout(e){
        const value = e.target.value;
        if (value.length > 140) {
            return;
        }
        this.setState({
            about: value
        });
    }
});
