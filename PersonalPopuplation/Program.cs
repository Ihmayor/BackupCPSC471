using Microsoft.VisualBasic.FileIO;
using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace PersonalPopuplation
{
    public class Program
    {

        static Random _random = new Random();


        public static void Main(string[] args)
        {
            //Populate Airport and Major Cities Tables with Information Collected from http://airlineupdate.com/
            insertAirportTableInfo("../../AllAirport.csv");
            //Populate Airline Tables with Information Collected from http://airlineupdate.com/. **Only Collected Canadian Airlines for Simplicity
            insertAirlineTableInfo("../../AllCanadianAirlines.csv");
            //Based on the airports generate a network of flights such that there is at least one flight going from each and every airport 
            createNetworkOfFlights();
            //Set the paths of each flight. One path for every flight A to B
            //Also generates some fligths with two paths for Airport A to Airport B to Airport C. 
            setPaths();
            //Generate the seats per flight for auction
            setSeats();
            //Randomly assign Airlines to different flights.
            setSales();
        }


    
        #region Actual Method for Inserts
        public static void setPaths()
        {
            //Get Flights
            using (DatabaseClassDataContext database = new DatabaseClassDataContext())
            {
                //Initial Association of Paths with Flights
                List<Flight> flightsFetched = database.Flights.ToList();
                //  List<Path> pathsCreated = new List<Path>();
                foreach (Flight f in flightsFetched)
                {
                    Path path = new Path()
                    {
                        airport_name = f.airport_departure_name,
                        airport2_name = f.airport_arrival_name,
                        distance = f.distance,
                        flight_id = f.Flight_id,
                    };
                    Console.WriteLine(path.path_no);
                    database.Paths.InsertOnSubmit(path);
                    database.SubmitChanges();
                }


                //Generate a few flights with more than one path.
                List<Airport> canadianAirports = database.Airports.Where(x => x.Major_City.country_name == "Canada").ToList();
                List<Airport> usAirports = database.Airports.Where(x => x.Major_City.country_name == "US").ToList();
                List<Airport> mexicoAirports = database.Airports.Where(x => x.Major_City.country_name == "Mexico").ToList();

                //US -Canada Flight Generation
                for (int i = 0; i < 5; i++)
                {
                    //Generate Flight with Random Departure from Canada and Random Arrival to the U.S
                    //Distance is randomly generated within an reasonable range
                    Flight flight1 = generateFlight(database, canadianAirports[_random.Next(0, canadianAirports.Count)], usAirports[_random.Next(0, usAirports.Count)], _random.Next(2000, 5000));
                    database.Flights.InsertOnSubmit(flight1);

                    //Generate the Paths to save flight information
                    Path p1 = new Path()
                    {
                        airport2_name = usAirports[_random.Next(0, usAirports.Count)].Name,
                        airport_name = flight1.airport_departure_name,
                        distance = flight1.distance - _random.Next(100, flight1.distance / 3),
                        flight_id = flight1.Flight_id,
                    };

                    Path p2 = new Path()
                    {
                        airport2_name = flight1.airport_arrival_name,
                        airport_name = p1.airport2_name,
                        distance = flight1.distance - p1.distance,
                        flight_id = flight1.Flight_id,
                    };
                    //Insert into canada
                    database.Paths.InsertOnSubmit(p1);
                    database.Paths.InsertOnSubmit(p2);
                }

                //Mexico Canada Flight Generation
                for (int i = 0; i < 2; i++)
                {
                    Flight flight1 = generateFlight(database, canadianAirports[_random.Next(0, canadianAirports.Count)], mexicoAirports[_random.Next(0, mexicoAirports.Count)], _random.Next(4000, 6000));
                    database.Flights.InsertOnSubmit(flight1);
                    Path p1 = new Path()
                    {
                        airport2_name = canadianAirports[_random.Next(0, canadianAirports.Count)].Name,
                        airport_name = flight1.airport_departure_name,
                        distance = flight1.distance - _random.Next(100, flight1.distance / 3),
                        flight_id = flight1.Flight_id,
                    };

                    Path p2 = new Path()
                    {
                        airport2_name = flight1.airport_arrival_name,
                        airport_name = p1.airport2_name,
                        distance = flight1.distance - p1.distance,
                        flight_id = flight1.Flight_id,
                    };

                    database.Paths.InsertOnSubmit(p1);
                    database.Paths.InsertOnSubmit(p2);
                }


                //Mexico-US Flight Generation
                for (int i = 0; i < 3; i++)
                {
                    Flight flight1 = generateFlight(database, usAirports[_random.Next(0, usAirports.Count)], usAirports[_random.Next(0, usAirports.Count)], _random.Next(3000, 4000));
                    database.Flights.InsertOnSubmit(flight1);
                    Path p1 = new Path()
                    {
                        airport2_name = usAirports[_random.Next(0, usAirports.Count)].Name,
                        airport_name = flight1.airport_departure_name,
                        distance = flight1.distance - _random.Next(100, flight1.distance / 3),
                        flight_id = flight1.Flight_id,
                    };

                    Path p2 = new Path()
                    {
                        airport2_name = flight1.airport_arrival_name,
                        airport_name = p1.airport2_name,
                        distance = flight1.distance - p1.distance,
                        flight_id = flight1.Flight_id,
                    };

                    database.Paths.InsertOnSubmit(p1);
                    database.Paths.InsertOnSubmit(p2);
                }

                database.SubmitChanges();

            }



        }

        public static void insertAirportTableInfo(string filename)
        {
            //Read File 
            using (TextFieldParser parser = new TextFieldParser(@"" + filename + ""))
            {
                parser.TextFieldType = FieldType.Delimited;
                parser.SetDelimiters(",");
                while (!parser.EndOfData)
                {
                    //Process row
                    string[] fields = parser.ReadFields();
                    if (fields[0] != "Country")
                    {
                        //Insert Row
                        string country = fields[0].Trim();
                        string city = fields[1].Trim();
                        string airportName = fields[2].Trim();
                        InsertMajorCityInDatabase(city, country).Wait();
                        InsertAirportInDatabase(city, airportName).Wait();
                    }

                }
            }

        }

        public static void insertAirlineTableInfo(string filename)
        {
            //Read File 
            using (TextFieldParser parser = new TextFieldParser(@"" + filename + ""))
            {
                parser.TextFieldType = FieldType.Delimited;
                parser.SetDelimiters(",");
                while (!parser.EndOfData)
                {
                    //Process row
                    string[] fields = parser.ReadFields();

                    //Insert Row
                    string city = fields[2].Trim();
                    string airportName = fields[1].Trim();
                    InsertAirlineInDatabase(city, airportName).Wait();

                }
            }

        }

        public static void createNetworkOfFlights()
        {
            //Start connection to database using .dbml 
            using (DatabaseClassDataContext database = new DatabaseClassDataContext())
            {
                //Create two instances of a list of cities to loop through
                List<Major_City> fetchedCities = database.Major_Cities.ToList();
                List<Major_City> fetchedCities2 = database.Major_Cities.ToList();
                fetchedCities2.Reverse();
                foreach (Major_City c in fetchedCities)
                {
                    foreach (Major_City c2 in fetchedCities2)
                    {
                        if (c2.name != c.name)
                        {

                            //Generate Flights inside Canada 
                            if (c2.country_name == "Canada" && c.country_name == "Canada")
                            {
                                int distance = _random.Next(250, 4501);//Distances created from range with a rounded furthest possible flight distance in canada to smallest possible flight distance in canada
                                InsertFlightIntoDatabase(distance, c, c2, database);
                            }
                            //Generate Flights inside United States 
                            else if (c2.country_name == "US" && c.country_name == "US")
                            {
                                int distance = _random.Next(250, 2093);
                                InsertFlightIntoDatabase(distance, c, c2, database);
                            }
                                //Generate Flights within Mexico
                            else if (c2.country_name == "Mexico" && c.country_name == "Mexico")
                            {
                                int distance = 15;//There's only two international airports in mexico so the distance a rounded 
                                InsertFlightIntoDatabase(distance, c, c2, database);

                            }
                                //Generate Flights from Mexico to canada
                            else if (c2.country_name == "Canada" && c.country_name == "Mexico")
                            {
                                int distance = 4500;
                                InsertFlightIntoDatabase(distance, c, c2, database);

                            }
                            else if (c2.country_name == "Canada" && c.country_name == "US")
                            {
                                int distance = _random.Next(500, 3501);
                                InsertFlightIntoDatabase(distance, c, c2, database);

                                //Generate Flights from US to Canada
                            }
                            else if (c2.country_name == "US" && c.country_name == "Canada")
                            {
                                int distance = _random.Next(500, 3501);
                                InsertFlightIntoDatabase(distance, c, c2, database);

                                //Generate Flights from Canada to US
                            }
                            //Generate Flights from US to Mexico
                            else if (c2.country_name == "US" && c.country_name == "Mexico")
                            {
                                int distance = _random.Next(400, 3501);
                                InsertFlightIntoDatabase(distance, c, c2, database);

                            }
                            //Generate Flights from Mexico to US
                            else if (c2.country_name == "Mexico" && c.country_name == "US")
                            {
                                int distance = _random.Next(400, 3501);
                                InsertFlightIntoDatabase(distance, c, c2, database);
                            }
                        }
                    }
                }
            }
        }
       
        private static void setSeats()
        {
            //Start Connection to Database
            using (DatabaseClassDataContext database = new DatabaseClassDataContext())
            {

                //Fetch List of Flights
                List<Flight> flightsFetched = database.Flights.ToList();
                int count = 0;
                foreach (Flight f in flightsFetched)
                {
                    //Loop and generate five seats per flights
                    for (int i = 0; i < 5; i++)
                    {
                        Seat s = new Seat()
                        {
                            seat_id = count.ToString(),
                            flight_id = f.Flight_id,
                            end_auction_date = DateTime.Now,
                        };
                        database.Seats.InsertOnSubmit(s);
                        database.SubmitChanges();
                        count++;
                    }
                }
            }
        }

        private static void setSales()
        {
            //Connect to Database
            using (DatabaseClassDataContext database = new DatabaseClassDataContext())
            {
                //Fetch Flights 
                List<Flight> flightsFetched = database.Flights.ToList();
                //Fetch Airlines 
                List<Airline_Company> airlines = database.Airline_Companies.ToList();
                
                //Loop through flights and randomly pick an airline to associate a airline_sale with that flight and airline.
                foreach (Flight f in flightsFetched)
                {
                    Airline_Sale sale = new Airline_Sale
                    {
                        Name = airlines[_random.Next(0, airlines.Count)].company_name,
                        Flight_id = f.Flight_id
                    };
                    if (database.Airline_Sales.Where(x => x.Flight_id == f.Flight_id).ToList().Count == 0)
                    {
                        database.Airline_Sales.InsertOnSubmit(sale);
                        database.SubmitChanges();
                    }
                }
            }
        }

        #endregion

        #region Database Methods
        //Used for taking read data from files and inserting airports into airport table
        public async static Task InsertAirportInDatabase(string setCityName, string airportName)
        {
            await Task.Factory.StartNew(() =>
            {
                using (DatabaseClassDataContext database = new DatabaseClassDataContext())
                {
                    PersonalPopuplation.Airport Airport = new PersonalPopuplation.Airport()
                    {
                        cityName = setCityName,
                        Name = airportName
                    };
                    PersonalPopuplation.Airport check = database.Airports.SingleOrDefault(x => x.Name.Equals(airportName));
                    if (check == null)
                    {
                        database.Airports.InsertOnSubmit(Airport);
                        database.SubmitChanges();
                    }
                }
            });
        }

        //Used for taking read data from files and inserting major cities  into MAJOR_CITY table
        public async static Task InsertMajorCityInDatabase(string cityName, string countryName)
        {
            await Task.Factory.StartNew(() =>
            {
                using (DatabaseClassDataContext database = new DatabaseClassDataContext())
                {
                    PersonalPopuplation.Major_City city = new PersonalPopuplation.Major_City()
                    {
                        name = cityName,
                        country_name = countryName
                    };
                    PersonalPopuplation.Major_City check = database.Major_Cities.SingleOrDefault(x => x.name.Equals(cityName));
                    if (check == null)
                    {
                        database.Major_Cities.InsertOnSubmit(city);
                        database.SubmitChanges();
                    }
                }
            });
        }

        //Used for taking read data from files and inserting airlines into Airline_Company Table
        public async static Task InsertAirlineInDatabase(string cityName, string airline)
        {
            await Task.Factory.StartNew(() =>
            {
                using (DatabaseClassDataContext database = new DatabaseClassDataContext())
                {
                    PersonalPopuplation.Airline_Company companyToAdd = new PersonalPopuplation.Airline_Company()
                    {
                        cityName = cityName,
                        company_name = airline
                    };
                    PersonalPopuplation.Airline_Company check = database.Airline_Companies.SingleOrDefault(x => x.company_name.Equals(airline));
                    if (check == null)
                    {

                        database.Airline_Companies.InsertOnSubmit(companyToAdd);
                        database.SubmitChanges();
                    }
                }
            });
        }

        //Used to insert flgiths and generate airports associated with that major city (since there can exist two of them per city) 
        public static void InsertFlightIntoDatabase(int distance, Major_City c, Major_City c2, DatabaseClassDataContext database)
        {
            int fetch = 0;
            int fetch2 = 0;
            if (database.Airports.Where(x => x.cityName.Equals(c.name)).ToList().Count > 1)
                fetch = _random.Next(0, 2);

            if (database.Airports.Where(x => x.cityName.Equals(c2.name)).ToList().Count > 1)
                fetch2 = _random.Next(0, 2);
            PersonalPopuplation.Airport departureAirport = database.Airports.Where(x => x.cityName.Equals(c.name)).ToList()[fetch];


            PersonalPopuplation.Airport arrivalAirport = database.Airports.Where(x => x.cityName.Equals(c2.name)).ToList()[fetch2];

            string deptAirportName = departureAirport.Name;
            string arrivalAirportName = arrivalAirport.Name;

            //Boolean Check to ensure that no two flights are saved with the same city, this was to prevent 
            //Overpopulating the database
            //if (database.FLIGHTs.Where(x => x.departure_airport.Equals(deptAirportName) && x.arrival_airport.Equals(arrivalAirportName)).ToList().Count> 0 )
            //{
            //    FLIGHT f = database.FLIGHTs.Where(x => x.departure_airport.Equals(deptAirportName) && x.arrival_airport.Equals(arrivalAirportName)).ToList()[0];
            //    Console.WriteLine("Flight Exists!");
            //    Console.WriteLine(f.arrival_time);
            //    Console.WriteLine(f.departure_time); 
            //    return;
            //}

            //Generates Flight Number for Flight's ID
            string flightid = generateFlightNumber();
            //Loops until a unique one is generated
            while (database.Flights.SingleOrDefault(x => x.Flight_id.Equals(flightid)) != null)
            {
                flightid = generateFlightNumber();
                Console.WriteLine("Generating New Unique Flight Number");
            }

            //Randomly generate a departure time
            DateTime deptTime = DateTime.Now.AddMinutes(30 * _random.Next(0, 4));
            PersonalPopuplation.Flight flight = new PersonalPopuplation.Flight()
            {
                Flight_id = flightid,
                airport_arrival_name = arrivalAirportName,
                airport_departure_name = deptAirportName,
                departure_time = deptTime,
                arrival_time = deptTime.AddMinutes(((double)distance / (double)926) * _random.Next(600, 1000)),
                distance = distance
            };
            database.Flights.InsertOnSubmit(flight);
            database.SubmitChanges();
        }


        #endregion

        #region Generate Data Methods

        //Extracted functionality from insert flight in database 
        //Used for path generation
        public static Flight generateFlight(DatabaseClassDataContext database, Airport a1, Airport a2, int distance)
        {
            string flightid = generateFlightNumber();
            while (database.Flights.SingleOrDefault(x => x.Flight_id.Equals(flightid)) != null)
            {
                flightid = generateFlightNumber();
                Console.WriteLine("Generating New Unique Flight Number");
            }
            DateTime deptTime = DateTime.Now.AddMinutes(30 * _random.Next(0, 4));

            PersonalPopuplation.Flight flight = new PersonalPopuplation.Flight()
            {
                Flight_id = flightid,
                airport_arrival_name = a1.Name,
                airport_departure_name = a2.Name,
                departure_time = deptTime,
                arrival_time = deptTime.AddMinutes((((double)distance / (double)926) * _random.Next(600, 1000)) * 3),
                distance = distance
            };
            return flight;
        }

        //Generates Flight Number according the real flight number format of XX{a}n{n}{n}{n}{a} where anything in brackets is optional
        //Does not lend itself to realism as more frequent flights ought to get smaller numbers
        //And flights themselves are not identified uniquely by flight id but also by their date and time
        //Because it is technially the same flight going from A-B. 
        //However, we did not account for such earlier so will have to sacrifice such realism.
        public static string generateFlightNumber()
        {
            string code = GetAirCodeLetter("x") + GetAirCodeLetter("x") + GetAirCodeLetter("ao") + GetAirCodeLetter("n") + GetAirCodeLetter("no") + GetAirCodeLetter("no") + GetAirCodeLetter("no") + GetAirCodeLetter("ao");
            code = code.Trim();
            while (code.Contains(" "))
            {
                code = code.Replace(" ", "");
                Console.WriteLine(code);
            }
            return code;
        }

        //Generate the letter/number accordingly, whether or not it be 1-9, a-z, 1-9 && a-z, or none according to the optional situation
        public static string GetAirCodeLetter(string specify)
        {
            int someCase;
            if (specify == "x")
            {
                someCase = _random.Next(0, 3);
            }
            else if (specify == "a")
            {
                someCase = _random.Next(0, 2);
            }
            else if (specify == "n")
            {
                someCase = 2;
            }
            else if (specify == "no")
            {
                int optional = _random.Next(0, 2);
                if (optional == 1)
                {
                    return ' '.ToString();
                }
                someCase = 2;

            }
            else if (specify == "ao")
            {
                int optional = _random.Next(0, 2);
                if (optional == 1)
                {
                    return ' '.ToString();
                }
                someCase = _random.Next(0, 2);
            }
            else
            {
                return " ";
            }


            switch (someCase)
            {
                case 0:
                    int num = _random.Next(0, 26); // Zero to 25
                    char let = (char)('a' + num);
                    return let.ToString();
                case 1:
                    int num1 = _random.Next(0, 26); // Zero to 25
                    char let1 = (char)('A' + num1);
                    return let1.ToString();
                case 2:
                    int num2 = _random.Next(0, 10); // Zero to 9
                    return num2.ToString();
            }
            return " ";
        }

        #endregion



    }
}
