import React, { Component } from 'react'
import { connect } from 'react-redux'
import Loader from './Loader'
import $ from 'jquery'
import Navigation from './Navigation'
import Functions from '../functions/Functions'
import styled from 'styled-components'
import DateRangePicker from '@wojtekmaj/react-daterange-picker'
import './css/DateRange.css'

class Account extends Component {
  constructor(props) {
    super(props);
    this.state = {
      loading: true,
      contract: '',
      date: [new Date(), new Date()],
      hidden: false
    }
  }

  com

  // асинхронные запросы на получения пользователя, договора и счетов.
  async componentDidMount() {
    this.setState({ loading: true })
    const self = this;
    const F = new Functions();
    const id = F.getCookie('user_id');

    // получаем пользователя
    await $.ajax({
      url: `${window.location.hostname === 'localhost' ? 'http://arenda.local' : window.location.origin}/api/renters/read_one.php`,
      data: { id: id },
      type: 'POST',
      success: function (res) {
        self.props.getRenter(res);
      },
      error: function (err) {
        console.log(err);
      }
    });

    // получаем договора
    await $.ajax({
      url: `${window.location.hostname === 'localhost' ? 'http://arenda.local' : window.location.origin}/api/contract/read_one.php`,
      data: { id: id },
      type: 'POST',
      success: function (res) {
        self.props.getContracts(res);
      },
      error: function (err) {
        console.log(err);
      }
    });

    // получаем счета
    await $.ajax({
      url: `${window.location.hostname === 'localhost' ? 'http://arenda.local' : window.location.origin}/api/invoice/read_by_renter_fullname.php`,
      data: { fullname: self.props.testStore.renters[0].full_name },
      type: 'POST',
      success: function (res) {
        self.props.getInvoices(res)
      },
      error: function (err) {
        console.log(err);
      }
    });

    // получаем пени
    await $.ajax({
      url: `${window.location.hostname === 'localhost' ? 'http://arenda.local' : window.location.origin}/api/peni/read_by_renter.php`,
      data: { shortname: self.props.testStore.renters[0].short_name },
      type: 'POST',
      success: function (res) {
        self.props.getPeni(res);    
      },
      error: function (err) {
        console.log(err);
      }
    });

    await this.setState({ loading: false });
  }

  // ставим в стейт выбранный для акта сверки договор
  handleChangeContract = (e) => this.setState({ contract: e.target.value })
  // выбор даты для акта сверки
  onChange = date => this.setState({ date });

  // Toggle при клике на арендатора и договора
  togglePeni = e => $(e.target).siblings('.peni-details').slideToggle();
  toggleInvoice = e => $(e.target).siblings('.invoice-details').slideToggle();

  dayOfMonth = () => new Date().getDate();

  isFirst = (start_date, invoice_date, days) => {
    const start = start_date.split('.').reverse(),
          invoice = invoice_date.split('-');

    if (start[1] === invoice[1] && start[0] === invoice[0]) {  
      return parseInt(start[2]) + parseInt(days);
    } else {
      return 5;
    }
  }

  recountBalance = (balance, summa, discount) => {
    setTimeout(() => {
      document.getElementById('balance').innerText = ' ' + (parseFloat(balance) + parseFloat(summa) - parseFloat(discount)).toFixed(2) + '₽';
    }, 0);
  }

  render() {
    const { loading, contract, date, hidden } = this.state;
    const renter = this.props.testStore.renters[0];
    const store = this.props.testStore;

    return (
      <React.Fragment>
        {loading ? <Loader></Loader> : (<React.Fragment>
          <Navigation />
          <div className='container' style={{ 'width': '700px ' }}>
            <div>
              <h3 style={{ 'marginBottom': '0' }}>{renter.full_name}</h3>
              <p style={{ 'marginTop': '0' }}>Текущий баланс: 
                <span id='balance' style={renter.balance >= 0 ? { 'color': 'green' } : { 'color': 'red' }}> 
                  {`${renter.balance} ₽`}
                </span>
              </p> 
            </div>

            <div style={{ 'marginTop': '40px ' }}>
              <Row>
                <b>№ Договора</b>
                <b>На сумму</b>
              </Row>
              <hr />
              {store.contracts.map((contract, i) => {
                return (
                  <Row key={i}>
                    <span><b>{contract.number} </b> за период: {contract.start_date} - {contract.end_date}</span>
                    <span>{contract.summa}</span>
                  </Row>
                )
              })}
            </div>

            <div style={{ 'marginTop': '40px ' }}>
              <Row>
                <b>Акт сверки</b>
                <DateRangePicker
                  onChange={this.onChange}
                  value={this.state.date}
                />
                <Select onChange={this.handleChangeContract}>
                  <option selected disabled hidden>Выберите договор</option>
                  {store.contracts.map((contract, i) => {
                    return (
                      <option key={i} value={contract.id}>№ {contract.number}, от {contract.datetime}</option>
                    )
                  })}
                </Select>
              </Row>
              <hr />

              {/*           {contract !== '' ? (<React.Fragment>
                <Row><b>Акт сверки</b>
                  <a 
                    href={`/schet-pechatnaya-forma?con=${renter.id}&ind=as&pr=0&start=${date[0].toLocaleDateString()}&end=${date[1].toLocaleDateString()}&id=${contract}`} 
                    target='_blank' 
                    rel="noopener noreferrer"  >Распечатать</a>
                </Row>
                <Row><b>Акт сверки + печать</b>
                  <a 
                    href={`/schet-pechatnaya-forma?con=${renter.id}&ind=as&pr=0&start=${date[0].toLocaleDateString()}&end=${date[1].toLocaleDateString()}&id=${contract}`} 
                    target='_blank' 
                    rel="noopener noreferrer"  >Распечатать</a>
                </Row>
              </React.Fragment>) : null} */}
            </div>

            {store.peni.length > 0 && !hidden
              ? (<React.Fragment>
                <div className='error-msg' style={{ 'position': 'relative', 'marginTop': '40px' }}>
                  <img src='/img/warning_white_48x48.png' alt='' />
                  <div className='error-msg-text'>
                    <p><strong>Внимание!</strong></p>
                    <p>У вас есть неоплаченные пени. <br /> Следующее поступление денежных средств на счёт, будет покрывать неоплаченные пени.</p>
                  </div>
                  <div className='close' onClick={() => this.setState({ hidden: true })}></div>
                </div>
                <div style={{ 'marginTop': '40px ' }}>
                  <Row>
                    <b>Все пени</b>
                  </Row>
                  <hr />
                  {store.peni.map((p, i) => {
                    return (
                      <Row key={i}>
                        <div style={{ 'width': '100%' }} onClick={this.togglePeni}>
                          <p className='renters-list-link toggle'>Пени №: {p.peni_invoice} за {p.month}, {p.year}, на сумму: {p.peni} (просрочка на: {p.delay} дней)</p>
                          <hr />
                          <div className='peni-details' style={{ 'display': 'none' }} data-block={p.renter_id}>
                            <Row>
                              <b>Счет</b>
                              <a href={`/schet-pechatnaya-forma?num=${p.peni_invoice}&ind=sch&pr=0&disc=0&peni=1`} target='_blank' rel="noopener noreferrer" >Распечатать</a>
                            </Row>
                            <Row>
                              <b>Акт</b>
                              <a href={`/schet-pechatnaya-forma?num=${p.peni_invoice}&ind=akt&pr=0&disc=0&peni=1`} target='_blank' rel="noopener noreferrer" >Распечатать</a>
                            </Row>
                            <Row>
                              <b>Счет-фактура</b>
                              <a href={`/schet-pechatnaya-forma?num=${p.peni_invoice}&ind=sf&pr=0&disc=0&peni=1`} target='_blank' rel="noopener noreferrer" >Распечатать</a>
                            </Row>
                            <Row>
                              <b>Счет+печать</b>
                              <a href={`/schet-pechatnaya-forma?num=${p.peni_invoice}&ind=sch&pr=1&disc=0&peni=1`} target='_blank' rel="noopener noreferrer" >Распечатать</a>
                            </Row>
                            <Row>
                              <b>Акт+печать</b>
                              <a href={`/schet-pechatnaya-forma?num=${p.peni_invoice}&ind=akt&pr=1&disc=0&peni=1`} target='_blank' rel="noopener noreferrer" >Распечатать</a>
                            </Row>
                            <Row>
                              <b>Счет-фактура+печать</b>
                              <a href={`/schet-pechatnaya-forma?num=${p.peni_invoice}}&ind=sf&pr=1&disc=0&peni=1`} target='_blank' rel="noopener noreferrer" >Распечатать</a>
                            </Row>
                          </div>
                        </div>
                      </Row>
                    )
                  })}
                </div>
              </React.Fragment>) : null}

            <div style={{ 'marginTop': '40px' }}>
              <b>Все выставленные счета</b>

              {store.invoices.map((invoice, i) => {
                return (
                  <React.Fragment key={i} >
                    <div onClick={this.toggleInvoice}>
                      <p className='toggle' style={{ 'marginTop': '30px', 'marginBottom': '0' }} >
                        № счёта: {invoice.invoice_number} за {invoice.period_month}, {invoice.period_year}, на сумму: 
                        { parseFloat(renter.balance) + parseFloat(invoice.summa) >= 0 
                          && !parseInt(invoice.modified)
                          && this.dayOfMonth() <= this.isFirst(invoice.start_arenda, invoice.invoice_date, invoice.discount_days) 
                          ? 
                          <span className='ml5'>{`${invoice.discount} (со скидкой)`}</span>
                          : 
                          invoice.summa}

                      </p>
                      <hr />
                      <div className='invoice-details' style={{ 'display': 'none' }} data-block={invoice.renter_id}>

                        { parseFloat(renter.balance) + parseFloat(invoice.summa) >= 0 
                          && !parseInt(invoice.modified)
                          && this.dayOfMonth() <= this.isFirst(invoice.start_arenda, invoice.invoice_date, invoice.discount_days) 
                          
                        ? (<React.Fragment>       

                          {this.recountBalance(renter.balance, invoice.summa, invoice.discount)}
                        
                          <Row>
                            <b>Счет со скидкой</b>
                            <a href={`/schet-pechatnaya-forma?num=${invoice.invoice_number}&ind=sch&pr=0&disc=1`} target='_blank' rel="noopener noreferrer" >Распечатать</a>
                          </Row>
                          <Row>
                            <b>Акт со скидкой</b>
                            <a href={`/schet-pechatnaya-forma?num=${invoice.invoice_number}&ind=akt&pr=0&disc=1`} target='_blank' rel="noopener noreferrer" >Распечатать</a>
                          </Row>
                          <Row>
                            <b>Счет-фактура со скидкой</b>
                            <a href={`/schet-pechatnaya-forma?num=${invoice.invoice_number}&ind=sf&pr=0&disc=1`} target='_blank' rel="noopener noreferrer" >Распечатать</a>
                          </Row>
                          <Row>
                            <b>Счет+печать со скидкой</b>
                            <a href={`/schet-pechatnaya-forma?num=${invoice.invoice_number}&ind=sch&pr=1&disc=1`} target='_blank' rel="noopener noreferrer" >Распечатать</a>
                          </Row>
                          <Row>
                            <b>Акт+печать со скидкой</b>
                            <a href={`/schet-pechatnaya-forma?num=${invoice.invoice_number}&ind=akt&pr=1&disc=1`} target='_blank' rel="noopener noreferrer" >Распечатать</a>
                          </Row>
                          <Row>
                            <b>Счет-фактура+печать со скидкой</b>
                            <a href={`/schet-pechatnaya-forma?num=${invoice.invoice_number}&ind=sf&pr=1&disc=1`} target='_blank' rel="noopener noreferrer" >Распечатать</a>
                          </Row>                
                        </React.Fragment>) : (<React.Fragment>
                          <Row>
                            <b>Счет</b>
                            <a href={`/schet-pechatnaya-forma?num=${invoice.invoice_number}&ind=sch&pr=0&disc=0`} target='_blank' rel="noopener noreferrer" >Распечатать</a>
                          </Row>
                          <Row>
                            <b>Акт</b>
                            <a href={`/schet-pechatnaya-forma?num=${invoice.invoice_number}&ind=akt&pr=0&disc=0`} target='_blank' rel="noopener noreferrer" >Распечатать</a>
                          </Row>
                          <Row>
                            <b>Счет-фактура</b>
                            <a href={`/schet-pechatnaya-forma?num=${invoice.invoice_number}&ind=sf&pr=0&disc=0`} target='_blank' rel="noopener noreferrer" >Распечатать</a>
                          </Row>
                          <Row>
                            <b>Счет+печать</b>
                            <a href={`/schet-pechatnaya-forma?num=${invoice.invoice_number}&ind=sch&pr=1&disc=0`} target='_blank' rel="noopener noreferrer" >Распечатать</a>
                          </Row>
                          <Row>
                            <b>Акт+печать</b>
                            <a href={`/schet-pechatnaya-forma?num=${invoice.invoice_number}&ind=akt&pr=1&disc=0`} target='_blank' rel="noopener noreferrer" >Распечатать</a>
                          </Row>
                          <Row>
                            <b>Счет-фактура+печать</b>
                            <a href={`/schet-pechatnaya-forma?num=${invoice.invoice_number}&ind=sf&pr=1&disc=0`} target='_blank' rel="noopener noreferrer" >Распечатать</a>
                          </Row>
                        </React.Fragment>)}
                      </div>
                    </div>
                  </React.Fragment>
                )
              })}
            </div>
          </div>
        </React.Fragment>)}

      </React.Fragment>
    )
  }
}

const Row = styled.div`
  display: flex;
  align-items: center;
  justify-content: space-between;
  span {
    margin-bottom: 5px;
  }
`;

const Select = styled.select`
  color: #fff;
  background: #232323;
  border: 0;
  padding: 10px 20px;
  border-radius: 10px;
`;


export default connect(
  state => ({
    testStore: state
  }),
  dispatch => ({
    getRenter: (renters) => {
      dispatch({ type: 'FETCH_SINGLE_RENTER', payload: renters })
    },
    getContracts: (contract) => {
      dispatch({ type: 'FETCH_CONTRACTS_BY_RENTER', payload: contract })
    },
    getInvoices: (invoices) => {
      dispatch({ type: 'FETCH_INVOICES_BY_RENTER', payload: invoices })
    },
    getPeni: (peni) => {
      dispatch({ type: 'FETCH_PENI_BY_RENTER', payload: peni })
    }
  })
)(Account);