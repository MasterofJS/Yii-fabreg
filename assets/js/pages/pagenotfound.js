export default React.createClass({

    render () {
        return (
            <div className="row page_not_found_">
                <div className="columns text-center">
                    <h1>Erro 404, deu zica!</h1>
                    <h4>A página que você quer encontrar não existe.</h4>
                    <p>Clique aqui para voltar à <Link to="/">Página principal</Link></p>
                </div>
            </div>
        );
    }
});