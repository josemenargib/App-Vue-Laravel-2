<?php

namespace Database\Seeders;

use App\Models\Web_empresas;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class web_empresaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Web_empresas::create([
            'razon_social' => 'Tu Empresa S.A.',
            'nit' => '123456789',
            'direccion' => 'Calle 123 #45-67',
            'telefono' => '1234567890',
            'ciudad' => 'Ciudad Ejemplo',
            'pais' => 'País Ejemplo',
            'representante_legal' => 'Juan Pérez',
            'url_banner' => 'https://www.tuempresa.com/banner.jpg',
            'mision' => 'Nuestra misión es educar con excelencia.',
            'vision' => 'Ser líderes en educación en la región.',
            'about' => 'Somos una empresa dedicada a la enseñanza de calidad en diversas áreas del conocimiento.',
            'latitud' => '-17',
            'longitud' => '-16',
            'historia' => 'CodeAcademy: Naciendo en un garaje, conquistando el mundo digital En el pequeño y desordenado garaje de un suburbio de Seattle, en 2010, dos jóvenes apasionados por la programación, Alex y Maya, soñaban con un mundo donde todos tuvieran acceso a la educación tecnológica. Cansados de los cursos universitarios rígidos y los tutoriales online incompletos, decidieron crear su propia plataforma de aprendizaje: CodeAcademy.Con una inversión inicial mínima y una gran dosis de entusiasmo, lanzaron su primer curso básico de HTML. La respuesta fue abrumadora. Pronto, estudiantes de todas las edades y orígenes se unían a CodeAcademy para aprender a construir sus propias páginas web.De un garaje al mundoEl boca a boca y las recomendaciones en línea hicieron que CodeAcademy creciera exponencialmente. Pronto, Alex y Maya tuvieron que abandonar el garaje y mudarse a una oficina más grande. El equipo se expandió, incorporando a expertos en programación, diseño y pedagogía.La oferta de cursos se diversificó rápidamente. Además de HTML, CSS y JavaScript, CodeAcademy comenzó a ofrecer cursos sobre lenguajes de programación más avanzados como Python, Ruby y Java. También incluyeron temas como desarrollo web backend, desarrollo de aplicaciones móviles y ciencia de datos.Innovación constante y comunidadCodeAcademy siempre se ha caracterizado por su enfoque innovador. Fueron pioneros en el uso de la gamificación para hacer el aprendizaje más divertido y motivador. Implementaron un sistema de puntos, insignias y niveles que permitía a los estudiantes seguir su progreso y competir con otros.Además, fomentaron una fuerte comunidad de estudiantes y mentores. Los estudiantes podían hacer preguntas, colaborar en proyectos y compartir sus conocimientos con otros. Esta comunidad se convirtió en un activo invaluable para CodeAcademy, ya que los estudiantes se ayudaban mutuamente a aprender y a superar los desafíos.El impacto globalHoy en día, CodeAcademy es una de las plataformas de aprendizaje de programación más grandes y reconocidas del mundo. Millones de personas en más de 190 países han utilizado sus cursos para adquirir nuevas habilidades y cambiar sus vidas.CodeAcademy ha demostrado que la educación tecnológica puede ser accesible y divertida para todos. Ha empoderado a personas de todos los orígenes a construir un futuro mejor para sí mismos y para la sociedad',
        ]);
    }
}
