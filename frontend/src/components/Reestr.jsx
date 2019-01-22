import React, { Component } from 'react'
import { connect } from 'react-redux'
import styled from 'styled-components'
import $ from 'jquery'
import Loader from './Loader'
import Functions from '../functions/Functions'

class Reestr extends Component {
  constructor(props){
    super(props);
    this.state = {
      visible: 1,
      loading: true,
    }
    this.getRenters = this.getRenters.bind(this);
  }


  componentDidMount() {
    var self = this;
    this.setState({loading: true})

    // получаем арендаторов
    $.ajax({ 
      url: `${window.location.hostname === 'localhost' ? 'http://arenda.local' : window.location.origin}/api/renters/read.php`,
      type: 'POST',
      success: function(res) {
        self.props.getRenters(res)
      },
      error: function(err) {
        console.log(err);
      }
    });

    const formData = new FormData();
    formData.append('visible', this.state.visible);
    // получаем договора
    $.ajax({ 
      url: `${window.location.hostname === 'localhost' ? 'http://arenda.local' : window.location.origin}/api/contract/read.php`,
      data        : formData,
      processData : false,
      contentType : false,
      type: 'POST',
      success: function(res) {
        self.props.getContracts(res)
      },
      error: function(err) {
        console.log(err);
      }
    });
    // симулируем загрузку, ставим прелоадер
    setTimeout(() => {
      this.setState({ loading: false });
      const F = new Functions();
      F.hideRenters(); // прячем арендаторов без договоров
    }, 500);
  }

  getRenters(e) {
    // получаем арендаторов (срабатывает при клике на кнопку "показать всех")
    e.preventDefault(); 
    var self = this;
    this.setState({loading: true})
    this.state.visible === 1 ? this.setState({ visible: 0 }) : this.setState({ visible: 1 });
    $.ajax({ 
      url: `${window.location.hostname === 'localhost' ? 'http://arenda.local' : window.location.origin}/api/renters/read.php`,
      type: 'POST',
      success: function(res) {
        self.props.getRenters(res)
      },
      error: function(err) {
        console.log(err);
      }
    });

    const formData = new FormData();
    formData.append('visible', this.state.visible === 1 ?  0 : 1);
    // получаем договора
    $.ajax({ 
      url: `${window.location.hostname === 'localhost' ? 'http://arenda.local' : window.location.origin}/api/contract/read.php`,
      data        : formData,
      processData : false,
      contentType : false,
      type: 'POST',
      success: function(res) {
        self.props.getContracts(res)
      },
      error: function(err) {
        console.log(err);
      }
    });
    // симулируем задержку получения данных
    // скрываем арендаторов без договора
    setTimeout(() => {
      this.setState({ loading: false });
      const F = new Functions();
      F.hideRenters();
    }, 500);
  }

  // Toggle при клике на арендатора и договора
  toggleRenterDetails = e => $(e.target).siblings('.renter-details').slideToggle();
  toggleContractDetails = e => $(e.target).siblings('.details').slideToggle();
  
  render() {
    const store = this.props.testStore;
    const { visible, loading } = this.state;

    return (
      <div className='container'>
        {/* если loading===true показываем лоадер, если нет то компонент*/}
        {loading ? <Loader></Loader> : (<>
        
        <div>
          <button onClick={this.getRenters} className='btn' style={{ 'marginBottom':'30px'}} id='showAllbtn'>
           {!visible ? 'Показать с действующими договорами' : 'Показать всех арендаторов'}
          </button>
        </div>

        {/* рендерим арендаторов (аналог smarty foreach) */}
        {store.renters.map((renter, i) => {                  
          return (  
          <RenterContainer key={`renter-${i}`} onClick={this.toggleRenterDetails} className='renter'>   
            <h2>{renter.short_name} — Баланс: 
              {renter.balance > 0 ? <span style={{'color':'green'}}> {renter.balance} ₽ </span> : <span style={{'color':'red'}}> {renter.balance} ₽</span>}
            </h2>
            <RenterDetails style={{'display':'none'}} className='renter-details'>
              <p><b>ИНН:</b> {renter.inn} / <b>КПП:</b> {renter.kpp}</p>
              <p><b>ОГРН/ОГРНИП:</b> {renter.ogrn}</p>
              <p><b>Юридический адрес:</b> {renter.uridich_address}</p>
              <p><b>Почтовый адрес:</b> {renter.post_adress}</p>
              <p><b>Телефон:</b> {renter.phone}</p>
              <p><b>E-mail:</b> {renter.email}</p>
              <p><b>Наименование банка:</b> {renter.bank_name}</p>
              <p><b>БИК:</b> {renter.bank_bik}</p>
              <p><b>Кор. счет:</b> {renter.bank_ks}</p>
              <p><b>Расчетный счет:</b> {renter.bank_rs}</p>
            </RenterDetails>

            {/* рендерим договора */}
            {store.contracts.map((contract, j) => {
              if (contract.renter_id === renter.id) {

                return (
                  <RenterContract data-status={contract.status} key={`contract-${j}`} className='renter-contract'>
                    <RenterContractDetails onClick={this.toggleContractDetails}>
                      
                      {contract.end_date !== '' 
                        ? <p>{contract.number} от {contract.datetime}</p> 
                        : <p>{contract.number} от {contract.datetime} <i>(безсрочно)</i></p>
                      }

                      {contract.status === '0' 
                        ? <span style={{'color':'red'}}>Завершенный</span> 
                        : contract.status === '0.5'
                        ? <span style={{'color':'yellow'}}>Действителен до: {contract.end_date}</span>
                        : <span style={{'color':'green'}}>Действующий</span>
                      }
                    </RenterContractDetails>
                      <div style={{'display':'none'}} className='details'>
                        <p><b>Помещение:</b> {contract.room_number}</p>
                        <p><b>Номер на схеме:</b> {contract.number_scheme}</p>
                        <p><b>Площадь:</b> {contract.square}</p>
                        <p><b>Сумма договора:</b> {contract.summa}</p>
                        <p><b>Срок Аренды:</b> с {contract.start_date} по {contract.end_date}</p>
                        <p><b>Размер пени:</b> {contract.peni}</p>
                        <p><b>Аренда начисляется с:</b> {contract.start_date}</p>
                      </div>
                  </RenterContract>
                )
              }   
            })}
          </RenterContainer>
          ) 
        })} 
        </>)}
      </div> 
    )
  }
}

const RenterContainer = styled.div`
  width: 600px;
  margin: 0 auto;
  text-align: center;
  h2 {
    font-size: 18px;
    padding: 10px 0;
    margin: 0;
    cursor: pointer;
    border-bottom: 1px solid #2323;
    :hover {
      background: lightblue;
      transition: .37s ease;
    }
  }
`;

const RenterDetails = styled.div`
  p {
    text-align: left;
    font-size: 14px;
    line-height: 7px;
  }
`;

const RenterContract = styled.div`
  p {
    text-align: left;
    font-size: 12px;
    line-height: 7px;
  }
`;

const RenterContractDetails = styled.div`
  display: flex;
  justify-content: space-between;
  align-items: center;
  cursor: pointer;
  padding: 0 10px;
  :hover {
    background: lightblue;
    transition: .37s ease;
  }
  div {
    padding: 0 10px;
  }
  p {
    font-size: 14px;
    pointer-events: none;
  }
`;

export default connect(
  state => ({
    testStore: state
  }),
  dispatch => ({
    getContracts: (contracts) => {
      dispatch({ type: 'FETCH_CONTRACTS', payload: contracts})
    },
    getRenters: (renters) => {
      dispatch({ type: 'FETCH_RENTERS', payload: renters})
    },
    getRooms: (rooms) => {
      dispatch({ type: 'FETCH_ROOMS', payload: rooms})
    }      
  })
)(Reestr);