
Let’s say we have a business requirement to register a Lead. The customer's desire to import data to the lead-register using CSV files.

The csv file has following fields:

firstname;lastname;email;phonenumber1;phonenumber2;comment;

**The application should have following functionality:**
- Register a new lead
- Delete a person from the lead-register
- Find a person in the lead-register
- Import of persons using a CSV-file

**Potential problem areas to think about:**
- Duplicated persons
- Data validation (email)

**Requirements are:**
- All commands are executed from CLI
- Application should not use any framework
- Include at least 3 unit tests
- Application Data should be stored in file
- Provide brief information what wasn’t implemented and what can be improved
