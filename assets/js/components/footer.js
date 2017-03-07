import React from 'react';

export default React.createClass({
    render () {
        return (
            <footer className="footer_">
                <div className="row">
                    <div className="medium-6 column">
                        <ul className="inline-list">
                            <li><Link to="/terms">Termos de Serviço</Link></li>
                            <li><Link to="/privacy">Termos de Privacidade</Link></li>
                            {/*<li><Link to="/policy">Policy & Safety</Link></li>*/}
                            <li><Link to="/contact">Sugestões</Link></li>
                        </ul>
                    </div>
                    <div className="medium-6 column">
                        <p className="copy_ small-only-text-center">&copy; {new Date().getFullYear()} Unicorno.com.br Todos os direitos reservados.</p>
                    </div>
                </div>
            </footer>
        );
    }
});