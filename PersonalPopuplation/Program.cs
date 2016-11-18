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

        static string SQLConnectionString { get { return string.Format("Server={0};Database={1};User Id={2};Password={3};", "136.159.7.84 ", "CPSC471_Fall2016_G7", "CPSC471_Fall2016_G7", "a\"-na9o$^`I&\"nw"); } }


        public static void Main(string[] args)
        {
            //insertAirportTableInfo("../../AllAirport.csv");
            //insertAirlineTableInfo("../../AllCanadianAirlines.csv");
            //createNetworkOfFlights();
            setPaths();
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

                    //   pathsCreated.Add(path);
                }


                //Generate a few flights with more than one path.
                List<Airport> canadianAirports = database.Airports.Where(x => x.Major_City.country_name == "Canada").ToList();
                List<Airport> usAirports = database.Airports.Where(x => x.Major_City.country_name == "US").ToList();
                List<Airport> mexicoAirports = database.Airports.Where(x => x.Major_City.country_name == "Mexico").ToList();

                //US -Canada

                for (int i = 0; i < 5; i++)
                {
                    Flight flight1 = generateFlight(database, canadianAirports[_random.Next(0, canadianAirports.Count)], usAirports[_random.Next(0, usAirports.Count)], _random.Next(2000, 5000));
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

                //Five
                //Mexico Canada
                //Two
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




                //Mexico-US
                //Three
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
            using (DatabaseClassDataContext database = new DatabaseClassDataContext())
            {
                List<Major_City> fetchedCities = database.Major_Cities.ToList();
                List<Major_City> fetchedCities2 = database.Major_Cities.ToList();
                fetchedCities2.Reverse();
                foreach (Major_City c in fetchedCities)
                {
                    foreach (Major_City c2 in fetchedCities2)
                    {
                        if (c2.name != c.name)
                        {

                            if (c2.country_name == "Canada" && c.country_name == "Canada")
                            {
                                int distance = _random.Next(250, 4501);
                                InsertFlightIntoDatabase(distance, c, c2, database);
                            }
                            else if (c2.country_name == "US" && c.country_name == "US")
                            {
                                int distance = _random.Next(250, 2093);
                                InsertFlightIntoDatabase(distance, c, c2, database);

                                //250- 2092
                            }
                            else if (c2.country_name == "Mexico" && c.country_name == "Mexico")
                            {
                                int distance = 300;
                                InsertFlightIntoDatabase(distance, c, c2, database);

                            }
                            else if (c2.country_name == "Canada" && c.country_name == "Mexico")
                            {
                                int distance = 4500;
                                InsertFlightIntoDatabase(distance, c, c2, database);

                            }
                            else if (c2.country_name == "Canada" && c.country_name == "US")
                            {
                                int distance = _random.Next(500, 3501);
                                InsertFlightIntoDatabase(distance, c, c2, database);

                                //If Canada-US 500 km - 3500 km
                            }
                            else if (c2.country_name == "US" && c.country_name == "Canada")
                            {
                                int distance = _random.Next(500, 3501);
                                InsertFlightIntoDatabase(distance, c, c2, database);

                                //If Canada-US 500 km - 3500 km
                            }
                            else if (c2.country_name == "US" && c.country_name == "Mexico")
                            {
                                int distance = _random.Next(400, 3501);
                                InsertFlightIntoDatabase(distance, c, c2, database);

                            }
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

        #endregion

        #region Database Methods
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

            //Boolean Check to ensure 
            //if (database.FLIGHTs.Where(x => x.departure_airport.Equals(deptAirportName) && x.arrival_airport.Equals(arrivalAirportName)).ToList().Count> 0 )
            //{
            //    FLIGHT f = database.FLIGHTs.Where(x => x.departure_airport.Equals(deptAirportName) && x.arrival_airport.Equals(arrivalAirportName)).ToList()[0];
            //    Console.WriteLine("Flight Exists!");
            //    Console.WriteLine(f.arrival_time);
            //    Console.WriteLine(f.departure_time); 
            //    return;
            //}



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

        public static void generateSellersOfFlights()
        {
            //Airlines will be randomly assigned (but chosen perhaps based on cities. If no such one. Air canada it is.) 
        }

        #endregion



    }
}
