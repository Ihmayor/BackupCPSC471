using Microsoft.VisualBasic.FileIO;
using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace DataScriptsCPSC471
{
    public class Program
    {

        static Random _random = new Random();

        static string SQLConnectionString { get { return string.Format("Server={0};Database={1};User Id={2};Password={3};", "136.159.7.84 ", "CPSC471_Fall2016_G7", "CPSC471_Fall2016_G7", "a\"-na9o$^`I&\"nw"); } }


        public static void Main(string[] args)
        {
            createNetworkOfFlights();
         //    insertAirportTableInfo("../../AllAirport.csv");
         //         insertAirlineTableInfo("../../AllCanadianAirlines.csv");
        }
        public async static Task InsertAirportInDatabase(string cityName, string airportName)
        {
            await Task.Factory.StartNew(() =>
            {
                using (DatabaseClassDataContext database = new DatabaseClassDataContext())
                {
                    DataScriptsCPSC471.AIRPORT Airport = new DataScriptsCPSC471.AIRPORT()
                    {
                        CityName = cityName,
                        Name = airportName
                    };
                    DataScriptsCPSC471.AIRPORT check = database.AIRPORTs.SingleOrDefault(x => x.Name.Equals(airportName));
                    if (check == null)
                    {
                        database.AIRPORTs.InsertOnSubmit(Airport);
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
                    DataScriptsCPSC471.MAJOR_CITY city = new DataScriptsCPSC471.MAJOR_CITY()
                    {
                        Name = cityName,
                        CountryName = countryName
                    };
                    DataScriptsCPSC471.MAJOR_CITY check = database.MAJOR_CITies.SingleOrDefault(x => x.Name.Equals(cityName));
                    if (check == null)
                    {
                        database.MAJOR_CITies.InsertOnSubmit(city);
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
                    DataScriptsCPSC471.COMPANY companyToAdd = new DataScriptsCPSC471.COMPANY()
                    {
                        CityName = cityName,
                        Name = airline
                    };
                    DataScriptsCPSC471.COMPANY check = database.COMPANies.SingleOrDefault(x => x.Name.Equals(airline));
                    if (check == null)
                    {

                        database.COMPANies.InsertOnSubmit(companyToAdd);
                        database.SubmitChanges();
                    }
                }
            });
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
                        InsertAirportInDatabase(city, airportName).Wait();
                        InsertMajorCityInDatabase(city, country).Wait();
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

        public static void createNetworkOfFlights()
        {
            using (DatabaseClassDataContext database = new DatabaseClassDataContext())
            {
                List<MAJOR_CITY> fetchedCities = database.MAJOR_CITies.ToList();
                foreach(MAJOR_CITY c in fetchedCities)
                {
                    foreach(MAJOR_CITY c2 in fetchedCities)
                    {
                        if (c2.Name!= c.Name)
                        {
                            if (c2.CountryName == "Canada" &&  c.CountryName=="Canada")
                            {
                                int distance = _random.Next(250, 4501);
                                AddFlight(distance, c, c2, database);
                            }
                            else if (c2.CountryName == "US" && c.CountryName == "US")
                            {
                                int distance = _random.Next(250, 2093);
                                AddFlight(distance, c, c2, database);
                         
                                //250- 2092
                            }
                            else if (c2.CountryName == "Mexico" && c.CountryName == "Mexico")
                            {
                                int distance = 300;
                                AddFlight(distance, c, c2, database);
                         
                            }
                            else if (c2.CountryName == "Canada" && c.CountryName == "Mexico")
                            {
                                int distance = 4500;
                                AddFlight(distance, c, c2, database);
                         
                            }
                            else if (c2.CountryName == "Canada" && c.CountryName == "US")
                            {
                                int distance = _random.Next(500, 3501);
                                AddFlight(distance, c, c2, database);
                         
                                //If Canada-US 500 km - 3500 km
                            }
                            else if (c2.CountryName == "US" && c.CountryName == "Canada")
                            {
                                int distance = _random.Next(500, 3501);
                                AddFlight(distance, c, c2, database);
                         
                                //If Canada-US 500 km - 3500 km
                            }
                            else if (c2.CountryName == "US" && c.CountryName == "Mexico")
                            {
                                int distance = _random.Next(400, 3501);
                                AddFlight(distance, c, c2, database);
                         
                            }
                            else if (c2.CountryName == "Mexico" && c.CountryName == "US")
                            {
                                int distance = _random.Next(400, 3501);
                                AddFlight(distance, c, c2, database);
                            }
                        }
                    }
                }
           
            }

            





        }

        public static void generateSellersOfFlights()
        {
            //Airlines will be randomly assigned (but chosen perhaps based on cities. If no such one. Air canada it is.) 
        }

        public static void AddFlight(int distance, MAJOR_CITY c, MAJOR_CITY c2,DatabaseClassDataContext database)
        {
            int fetch = 0;
            int fetch2 = 0;
            if (database.AIRPORTs.Where(x => x.CityName.Equals(c.Name)).ToList().Count > 1)
                fetch = _random.Next(0, 2);

            if (database.AIRPORTs.Where(x => x.CityName.Equals(c2.Name)).ToList().Count > 1)
                fetch2 = _random.Next(0, 2);
            DataScriptsCPSC471.AIRPORT departureAirport = database.AIRPORTs.Where(x => x.CityName.Equals(c.Name)).ToList()[fetch];


            DataScriptsCPSC471.AIRPORT arrivalAirport = database.AIRPORTs.Where(x => x.CityName.Equals(c2.Name)).ToList()[fetch2];

            string deptAirportName = departureAirport.Name;
            string arrivalAirportName = arrivalAirport.Name;
           
            for (int i = 0; i < 24; i++)
            {
                string flightid = generateFlightNumber();
                while (database.FLIGHTs.SingleOrDefault(x => x.Flight_id.Equals(flightid)) != null)
                {
                    flightid = generateFlightNumber();
                    Console.WriteLine("Generating New Unique Flight Number");
                }
                DataScriptsCPSC471.FLIGHT companyToAdd = new DataScriptsCPSC471.FLIGHT()
                {
                    Flight_id = flightid,
                    arrival_airport = arrivalAirportName,
                    departure_airport = deptAirportName,
                    departure_time = DateTime.Now.AddMinutes(30 * i),
                    arrival_time = DateTime.Now.AddMinutes((distance / 926) * 60),
                    distance = distance,
                    base_price = 600
                };
            }
        }

    }
}
