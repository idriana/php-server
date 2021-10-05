#wrong page
curl -v --request POST http://localhost:8006/user_input.php?page=page --header "X-Access-Token:SECRET_TOKEN"
read -p "Press Enter to resume ..."