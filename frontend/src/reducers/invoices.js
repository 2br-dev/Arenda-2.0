export default function arenda(state = [], action) {
  switch (action.type) {
    case 'FETCH_ALL_INVOICES':
      return [...action.payload] 
    case 'DELETE_INVOICE':
      return [...action.payload]  
    case 'CLEAR_INVOICES':
      return []  
    case 'FETCH_INVOICES_BY_RENTER':
      return [...action.payload]           
    case 'FETCH_INVOICES_BY_ID':
      return [...action.payload]    
    default:
      return state
  }
}