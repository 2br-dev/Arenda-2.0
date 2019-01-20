export default function arenda(state = [], action) {
  switch (action.type) {
    case 'FETCH_ALL_INVOICES':
      return [...action.payload] 
    case 'DELETE_INVOICE':
      return [...action.payload]   
    default:
      return state
  }
}