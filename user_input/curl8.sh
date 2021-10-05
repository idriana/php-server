#ok
curl -v --request POST http://localhost:8006/user_input.php?page=page1 --header "X-Access-Token:SECRET_TOKEN" --header "Content-Type: application/x-www-form-urlencoded" --data var1=1 --data var2=2 --data var3=3
read -p "Press Enter to resume ..."