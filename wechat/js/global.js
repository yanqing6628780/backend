var BASE_URL = '/ci/api/my_interface/';
var AuthHeader = {'Authorization': 'Basic c3F0OllXYVdNVEl6TkE='};
var postDataHeader = {'Content-Type': 'application/x-www-form-urlencoded','Authorization': 'Basic c3F0OllXYVdNVEl6TkE='};

function getApiUrl(string){
	return BASE_URL + string;
}