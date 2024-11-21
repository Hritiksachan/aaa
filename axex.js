 
document.getElementById('fetchDataBtn').addEventListener('click', fetchData);

function fetchData() {
   
  fetch('https://jsonplaceholder.typicode.com/users')
    .then(response => {
       
      if (!response.ok) {
        throw new Error('Network response was not ok');
      }
      return response.json();  
    })
    .then(data => {
       
      displayData(data);
    })
    .catch(error => {
      console.error('Error:', error);
      alert('An error occurred while fetching data.');
    });
}

function displayData(data) {
   
  const container = document.getElementById('dataContainer');
  
   
  container.innerHTML = '';
 
  data.forEach(user => {
    const userDiv = document.createElement('div');
    userDiv.innerHTML = `
      <h3>${user.name}</h3>
      <p>Email: ${user.email}</p>
      <p>Company: ${user.company.name}</p>
    `;
    container.appendChild(userDiv);
  });
}
