<?php
echo phpinfo();
// meizitop
function fibonacci(uint number) constant returns(uint result) {
	if(number == 0) return 0;
	else if(number == 1) return 1;
	else return Fibonacci.fibonacci(number -1) + Fibonacci.fibonacci(number -2) ;
}

geth account new

geth account list 

geth account update 






contract queue  {  
      struct Queue {  
          uint[] data;  
          uint front;  
          uint back;  
      }  
      /// @dev the number of elements stored in the queue.  
      function length(Queue storage q) constant internal returns (uint) {  
          return q.back - q.front;  
      }  
          /// @dev the number of elements this queue can hold  
          function capacity(Queue storage q) constant internal returns (uint) {  
              return q.data.length - 1;  
          }  
          /// @dev push a new element to the back of the queue  
          function push(Queue storage q, uint data) internal  
          {  
              if ((q.back + 1) % q.data.length == q.front)  
                  return; // throw;  
              q.data[q.back] = data;  
              q.back = (q.back + 1) % q.data.length;  
          }  
          /// @dev remove and return the element at the front of the queue  
          function pop(Queue storage q) internal returns (uint r)  
          {  
              if (q.back == q.front)  
                  return; // throw;  
              r = q.data[q.front];  
              delete q.data[q.front];  
              q.front = (q.front + 1) % q.data.length;  
          }  
}
