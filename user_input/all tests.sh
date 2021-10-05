#wrong method
curl -v --request GET  'http://localhost:8006/user_input.php'
read -p "Press Enter to resume ..."

#no token
curl -v --request POST http://localhost:8006/user_input.php?page=page1
read -p "Press Enter to resume ..."

#wrong token
curl -v --request POST http://localhost:8006/user_input.php?page=page1 --header "X-Access-Token:SECRET_TOKE"
read -p "Press Enter to resume ..."

#no page
curl -v --request POST http://localhost:8006/user_input.php? --header "X-Access-Token:SECRET_TOKEN"
read -p "Press Enter to resume ..."

#wrong page
curl -v --request POST http://localhost:8006/user_input.php?page=page --header "X-Access-Token:SECRET_TOKEN"
read -p "Press Enter to resume ..."

#wrong datatype
curl -v --request POST http://localhost:8006/user_input.php?page=page1 --header "X-Access-Token:SECRET_TOKEN"
read -p "Press Enter to resume ..."

#no data
curl -v --request POST http://localhost:8006/user_input.php?page=page1 --header "X-Access-Token:SECRET_TOKEN" --header "Content-Type: application/x-www-form-urlencoded"
read -p "Press Enter to resume ..."

#ok
curl -v --request POST http://localhost:8006/user_input.php?page=page1 --header "X-Access-Token:SECRET_TOKEN" --header "Content-Type: application/x-www-form-urlencoded" --data var1=1 --data var2=2 --data var3=3
read -p "Press Enter to resume ..."