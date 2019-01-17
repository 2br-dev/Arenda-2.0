import React, { Component } from 'react'

export default class Reestr extends Component {
  constructor(props) {
    super(props);
    this.state = {
      renters: [],
      contracts: [],
      rooms: [],
      roomsInContracts: []
    }
  }

  componentDidMount() {
    // получаем арендаторов
    fetch(`http://arenda.local/api/renters/read.php`)
      .then(res => res.json())
      .then(json => this.setState({ renters: json.renters }))
      .catch(() => console.log('some error occured'))

/*     // получаем договора
    fetch(`http://arenda.local/api/contract/read.php`)
      .then(res => res.json())
      .then(json => this.setState({ contracts: json.contracts }))
      .catch(() => console.log('some error occured'))

    // получаем комнаты
    fetch(`http://arenda.local/api/room/read.php`)
      .then(res => res.json())
      .then(json => this.setState({ rooms: json.rooms }))
      .then(this.createRoomsArray)
      .catch(() => console.log('some error occured'))  */
  }
/* 
  //комнат в массив
  createRoomsArray = () => {
    const roomsInContracts = this.state.contracts;
    
    for (let i = 0; i < roomsInContracts.length; i++) {
      roomsInContracts[i] = roomsInContracts[i].rooms.split(',');
    }

    this.setState({ roomsInContracts });
  } */

  render() {
    const { contracts, renters, roomsInContracts, rooms } = this.state

    return (
      <div>
        <div>
          <button>Показать все договора</button>
        </div>

        {renters.map((renter, i) => {                  
          return (
          <div key={`renter-${i}`}>   
            <h2>{renter.short_name} — Баланс: 
              {renter.balance < 0 ? <span style={{'color':'green'}}> {renter.balance} ₽ </span> : <span style={{'color':'red'}}> {renter.balance} ₽</span>}
            </h2>
            <div>
              <p><b>{renter.full_name}</b></p>
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
            </div>

            {/*contracts.map((contract, i) => {
              if (contract.renter === renter.id) {

                return (
                  <div key={`contract-${i}`}>
                    <div>
                      
                      {contract.end_date !== '' 
                        ? <p>{contract.contract_number} от {contract.datetime}</p> 
                        : <p>{contract.contract_number} от {contract.datetime} <i>(безсрочно)</i></p>
                      }

                      {contract.status === '0' 
                        ? <span>Завершенный</span> 
                        : contract.status === '0.5'
                        ? <span>Действителен до: {contract.end_date}</span>
                        : <span>Действующий</span>
                      }
                    </div>
                    <div>
                      {roomsInContracts.map((room, i) => {
                        for(let j = 0; j < room.length; j++) {
                          if (room[j] == rooms.id) {
                            return (
                              <div key={`room-${i}`}>
                                <p><b>Помещение:</b> {rooms[room[j]].room_number}</p>
                                <p><b>Номер на схеме:</b> {rooms[room[j]].number_scheme}</p>
                                <p><b>Площадь:</b> {rooms[room[j]].square}</p>
                              </div>
                        )}}
                      })}  
                      <p><b>Сумма договора:</b> {contract.summa}</p>
                      <p><b>Срок Аренды:</b> с {contract.start_date} по {contract.end_date}</p>
                      <p><b>Размер пени:</b> {contract.peni}</p>
                      <p><b>Аренда начисляется с:</b> {contract.start_date}</p>
                    </div>
                  </div>
                )
              }               
            })*/}
          </div>
          ) 
        })} 
      </div>
    )
  }
}

