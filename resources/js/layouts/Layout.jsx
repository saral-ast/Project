import Sidebar from '../components/sidebar';

export default function Layout({ children }) {
    // const { auth, shop } = usePage().props;
    console.log('children  1:',children);
    return (
        <>  
              <Sidebar/>     
              <main>{children}</main>
        </>
    );
}
